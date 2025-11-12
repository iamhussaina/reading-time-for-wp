<?php
/**
 * Hussainas Reading Time Utility
 *
 * Provides procedural functions to calculate and display the estimated
 * reading time for WordPress posts.
 *
 * @package     HussainAS_Reading_Time_Utility
 * @version     1.0.0
 * @author      Hussain Ahmed Shrabon
 * @license     MIT
 * @link        https://github.com/iamhussaina
 * @textdomain  hussainas
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calculates the estimated reading time in minutes for a given post.
 *
 * This function retrieves the post content, strips tags and shortcodes,
 * counts the words, and calculates the reading time based on an average WPM.
 *
 * @param int|WP_Post|null $post Optional. Post ID or WP_Post object. Defaults to global $post.
 * @return int The estimated reading time in minutes (integer).
 */
function hussainas_calculate_reading_time_minutes( $post = null ) {
	// Get the post object from the provided ID or global $post.
	$post = get_post( $post );

	// Return 0 if the post object is invalid.
	if ( ! $post ) {
		return 0;
	}

	// Get the raw post content.
	$content = $post->post_content;

	// Remove shortcodes and all HTML tags.
	$content = strip_shortcodes( $content );
	$content = wp_strip_all_tags( $content );

	// Decode HTML entities.
	$decoded_content = html_entity_decode( $content );

	// Count the words using a regex split on whitespace.
	// This is more reliable for multi-byte characters (i18n) than str_word_count.
	$word_count = count( preg_split( '/\s+/', $decoded_content, -1, PREG_SPLIT_NO_EMPTY ) );

	// Return 0 if no words are found.
	if ( $word_count <= 0 ) {
		return 0;
	}

	/**
	 * Filters the Words Per Minute (WPM) rate used for calculation.
	 *
	 * @since 1.0.0
	 *
	 * @param int $wpm The WPM rate. Default 200.
	 */
	$wpm = (int) apply_filters( 'hussainas_reading_time_wpm', 200 );

	// Prevent division by zero if the filter returns 0 or less.
	if ( $wpm <= 0 ) {
		$wpm = 200;
	}

	// Calculate the reading time in minutes.
	$minutes = $word_count / $wpm;

	// Round up to the nearest minute.
	$estimated_time = (int) ceil( $minutes );

	// Ensure that if there is content, it shows at least "1 min read".
	if ( $estimated_time < 1 && $word_count > 0 ) {
		$estimated_time = 1;
	}

	return $estimated_time;
}

/**
 * Retrieves the formatted reading time string (e.g., "5 min read").
 *
 * This function returns the human-readable string.
 * Uses WordPress's translation functions for "min read" for professional standards.
 *
 * @param int|WP_Post|null $post Optional. Post ID or WP_Post object. Defaults to global $post.
 * @return string The formatted reading time string. Returns empty string if time is 0.
 */
function hussainas_get_estimated_reading_time( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	// Get the calculated minutes.
	$minutes = hussainas_calculate_reading_time_minutes( $post );

	// Return an empty string if reading time is 0.
	if ( $minutes <= 0 ) {
		return '';
	}

	// Prepare the translatable string.
	// This is the standard, professional way to handle strings in WordPress.
	$time_string = sprintf(
		/* translators: %d: number of minutes. */
		_n(
			'%d min read',
			'%d min read',
			$minutes,
			'hussainas' // Text domain
		),
		$minutes
	);

	/**
	 * Filters the final formatted reading time string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $time_string The formatted string (e.g., "5 min read").
	 * @param int    $minutes     The calculated minutes (integer).
	 * @param int    $post_id     The ID of the post.
	 */
	return apply_filters( 'hussainas_formatted_reading_time', $time_string, $minutes, $post->ID );
}

/**
 * Displays (echoes) the formatted reading time string.
 *
 * This is a helper function that wraps hussainas_get_estimated_reading_time()
 * and echoes its output, similar to the_title() vs. get_the_title().
 *
 * @param int|WP_Post|null $post Optional. Post ID or WP_Post object. Defaults to global $post.
 */
function hussainas_display_estimated_reading_time( $post = null ) {
	// We use wp_kses_post to ensure the output is safe,
	// although the get function should already be safe. This is best practice.
	echo wp_kses_post( hussainas_get_estimated_reading_time( $post ) );
}
