<?php
/**
 * Plugin Name: Post Visit Tally
 * Description: Counts unique visitors per post and displays the total on single post pages.
 * Version:      1.0
 * Author: Larry Judd
 * Requires PHP: 7.4
 * Requires CP:  2.4
 * Tested Up To: 6.8
 * Requires at least: 4.9
 * Text Domain:  post-visit-tally
 * Domain Path:  /languages
 * License:      GPLv2 or up
 * License URI:  License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
//define( 'POST_VISIT_VERSION', '1.0.0' );

//load language scripts     
function onlist_load_text_domain() 
{
    load_plugin_textdomain( 'post-visit-tally', false, 
                            basename( dirname( __FILE__ ) ) . '/languages' ); 
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-profile-details-activator.php
 */ 

function activate_post_visit_tally_tsw() 
{
    return false;
} 
register_activation_hook(   __FILE__, 'activate_post_visit_tally_tsw' );


// 1. Create the database table on activation
register_activation_hook( __FILE__, 'pvt_create_table' );
function pvt_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'post_visit_tally';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        visitor_ip varchar(45) NOT NULL,
        visit_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY unique_visit (post_id, visitor_ip)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

// 2. Logic to track the visit
add_action( 'wp_head', 'pvt_track_visitor' );
function pvt_track_visitor() {
    if ( is_single() ) {
        global $wpdb, $post;
        //$table_name = $wpdb->prefix . 'post_visit_tally';
        $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
        $post_id = $post->ID;

        // Insert using IGNORE or REPLACE logic to ensure "Unique" visitors
        $wpdb->query( $wpdb->prepare(
            "INSERT IGNORE INTO $wpdb->prefix . 'post_visit_tally' (post_id, visitor_ip) 
            VALUES (%d, %s)",
            absint($post_id),
            sanitize_text_field($ip)
        ));
    }
}

// 3. Retrieve and display the count
add_filter( 'the_content', 'pvt_display_tally' );
function pvt_display_tally( $content ) {
    if ( is_single() && in_the_loop() && is_main_query() ) {
        global $wpdb, $post;
        $table_name = $wpdb->prefix . 'post_visit_tally';
        
        $count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE post_id = %d",
            $post_id = $post->ID
        ));

        $tally_html  = '<div class="pvt-tally" style="margin-top:20px; padding:10px; border-top:1px solid #eee; font-style:italic;">';
        $tally_html .= sprintf( 
            esc_html__('Unique Visitors: %d .', 'post-visit-tally' ),
            absint( $count )
        );
        $tally_html .= '</div>';

        return $content . $tally_html;
    }
    return $content;
}