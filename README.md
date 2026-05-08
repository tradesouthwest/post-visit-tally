![Post Visit Plugin banner](images/banner-1544x500-post-visit-tally.png)

# Post Visit Tally
Dispaly unique visitor count to the bottom of single posts' pages.

- Requires PHP: 7.4
- Requires CP:  1.4
- Version:      1.0.1
- Author:       YourNameIdentity
- Tags:         scheduling, booking, appointments, translation-ready
- License:      GPL 3 (see LICENSE)
- Text domain:  hello-plugin
- Plugin URL:   https://github.com/tradesouthwest/post-visit-tally

## Description

Plugin for ClassicPress and WordPress to dispaly unique visitor count to the bottom of single posts' pages.

## Key Features and Architecture

    Database Efficiency: Instead of using update_post_meta, which can bloat the postmeta table on high-traffic sites, this uses a dedicated custom table. This is much faster for queries and easier to clean up.

    Uniqueness Enforcement: The database schema includes a UNIQUE KEY on the combination of post_id and visitor_ip. The INSERT IGNORE command ensures that if the same person refreshes the page, their hit isn't counted twice.

    Minimal Footprint: The plugin only runs the tracking logic on is_single() pages, preventing unnecessary overhead on the homepage or archive pages.

    Formatting: The count is appended to the bottom of the post content via the the_content filter, ensuring it remains compatible with most themes.

## Support
Use https://github.com/tradesouthwest/post-visit-tally/issues to post your issues with this plugin.

## Change Log
- 1.0.0
* initial release

## Implementation Notes

    Privacy: Since this records IP addresses, ensure your privacy policy reflects that you are collecting this data for analytics.

    Caching: If you use a heavy caching plugin (like WP Rocket or W3 Total Cache), the wp_head hook might not fire on every visit. In those cases, tracking is usually handled via a REST API endpoint and JavaScript to bypass the cache.

    CSS is added to theme or Customizer for lean execution.

    ```
    .pvt-tally {
    display: inline-block;
    background: #f4f4f4;
    color: #333;
    padding: 5px 12px !important;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
    border: none !important; /* Overrides the inline border if needed */
    margin-top: 15px;
    }

    ```

