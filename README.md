# Reading Time Utility for Wordpress

A lightweight, PHP utility for WordPress themes to calculate and display the estimated reading time for posts.

This utility is designed to be included in a theme's `functions.php` file or as part of a theme's library.

## Features

* Calculates reading time based on post word count.
* Configurable Words Per Minute (WPM) rate via a WordPress filter (`hussainas_reading_time_wpm`).
* Provides easy-to-use template functions:
    * `hussainas_display_estimated_reading_time()`
    * `hussainas_get_estimated_reading_time()`
* Follows WordPress coding standards and i18n best practices.
* Lightweight and procedural, with no complex classes or dependencies.

## Requirements

* WordPress 5.0 or higher
* PHP 7.4 or higher

## Installation

1.  **Download:** Download the `hussainas-reading-time.php` file from this repository.
2.  **Include Utility:** Open your theme's `functions.php` file and add the following PHP code:

## How to Use

This utility provides two template functions to display the reading time. You should use them inside your theme's template files (like `single.php`, `content.php`, or `archive.php`) within the WordPress Loop.

### Quick Example

To display the reading time, add this to your template file:

```php
<div class="post-meta">
    <span class="post-date"><?php echo get_the_date(); ?></span>
    
    <?php if ( function_exists( 'hussainas_display_estimated_reading_time' ) ) : ?>
        <span class="reading-time">
            <?php hussainas_display_estimated_reading_time(); ?>
        </span>
    <?php endif; ?>
</div>

You can customize the utility's behavior using WordPress filters.

## Changing the WPM (Words Per Minute)

By default, the calculation uses 200 WPM. You can change this by adding a filter.
Example: To change the WPM to 220:

```php
add_filter( 'hussainas_reading_time_wpm', 'my_custom_wpm_rate' );
function my_custom_wpm_rate( $wpm ) {
    // Set the new WPM rate
    return 220;
}

## Customizing the Output String
Example: To change the output to "Approx. 5 minutes":

```php
add_filter( 'hussainas_formatted_reading_time', 'my_custom_reading_time_format', 10, 3 );
function my_custom_reading_time_format( $time_string, $minutes, $post_id ) {
    // $time_string is the original string, e.g., "5 min read"
    // $minutes is the integer, e.g., 5
    // $post_id is the ID of the post being processed

    if ( $minutes === 1 ) {
         return 'Approx. 1 minute';
    }
    
    return sprintf( 'Approx. %d minutes', $minutes );
}

