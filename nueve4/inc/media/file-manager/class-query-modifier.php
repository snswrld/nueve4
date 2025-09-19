<?php
/**
 * Query Modifier Class for Nueve4 Theme
 *
 * Handles modification of media library queries to filter attachments by folder.
 * Integrates with WordPress media queries and AJAX requests.
 *
 * @package Nueve4
 * @subpackage Media\Query_Modifier
 * @since 3.0.0
 * @author kemetica.io
 * @license GPL-2.0-or-later
 */

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

namespace Nueve4\Media;

/**
 * Query Modifier Class
 *
 * Modifies WordPress media queries to filter attachments by folder assignment.
 * Handles both standard admin queries and AJAX media library requests.
 *
 * @since 3.0.0
 */
class Query_Modifier {

	/**
	 * Singleton instance
	 *
	 * @var Query_Modifier|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * Ensures only one instance of the Query_Modifier class exists.
	 *
	 * @since 3.0.0
	 * @return Query_Modifier The singleton instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the query modifier
	 *
	 * Registers WordPress hooks for modifying media queries
	 * in both admin and AJAX contexts.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function init() {
		// Hook into query modification for admin pages
		add_action( 'pre_get_posts', array( $this, 'modify_media_query' ) );
		
		// Hook into AJAX media library queries
		add_filter( 'ajax_query_attachments_args', array( $this, 'filter_media_query' ) );
	}

	/**
	 * Modify media queries for folder filtering
	 *
	 * Modifies the main query on admin pages to filter attachments
	 * by folder when a folder parameter is present.
	 *
	 * @since 3.0.0
	 * @param WP_Query $query The WordPress query object
	 * @return void
	 */
	public function modify_media_query( $query ) {
		// Only modify admin queries
		if ( ! is_admin() ) {
			return;
		}

		// Only modify main queries
		if ( ! $query->is_main_query() ) {
			return;
		}

		// Only modify attachment queries
		if ( 'attachment' !== $query->get( 'post_type' ) ) {
			return;
		}

		// Get folder parameter from request
		$folder = $this->get_folder_from_request();
		
		if ( $folder ) {
			$this->apply_folder_filter( $query, $folder );
		}
	}

	/**
	 * Filter AJAX media library queries
	 *
	 * Modifies AJAX query arguments for media library requests
	 * to filter attachments by folder.
	 *
	 * @since 3.0.0
	 * @param array $args Query arguments for attachment query
	 * @return array Modified query arguments
	 */
	public function filter_media_query( $args ) {
		// Validate arguments array
		if ( ! is_array( $args ) ) {
			return $args;
		}

		// Get folder parameter from request
		$folder = $this->get_folder_from_request();

		if ( $folder ) {
			// Add meta query for folder filtering
			$meta_query = array(
				array(
					'key'     => 'nueve4_folder',
					'value'   => $folder,
					'compare' => '=',
				),
			);

			// Merge with existing meta queries if present
			if ( isset( $args['meta_query'] ) && is_array( $args['meta_query'] ) ) {
				$args['meta_query'] = array_merge( $args['meta_query'], $meta_query );
			} else {
				$args['meta_query'] = $meta_query;
			}
		}

		return $args;
	}

	/**
	 * Get folder parameter from request
	 *
	 * Safely retrieves and validates the folder parameter from
	 * the current request (GET or POST).
	 *
	 * @since 3.0.0
	 * @return string|null The folder ID or null if not present/valid
	 */
	private function get_folder_from_request() {
		$folder = null;

		// Check POST request first
		if ( isset( $_POST['folder'] ) ) {
			$folder = sanitize_text_field( wp_unslash( $_POST['folder'] ) );
		}
		// Check GET request as fallback
		elseif ( isset( $_GET['folder'] ) ) {
			$folder = sanitize_text_field( wp_unslash( $_GET['folder'] ) );
		}

		// Validate folder value
		if ( empty( $folder ) || ! $this->is_valid_folder_id( $folder ) ) {
			return null;
		}

		return $folder;
	}

	/**
	 * Apply folder filter to query
	 *
	 * Applies the folder meta query filter to a WP_Query object.
	 *
	 * @since 3.0.0
	 * @param WP_Query $query  The query object to modify
	 * @param string   $folder The folder ID to filter by
	 * @return void
	 */
	private function apply_folder_filter( $query, $folder ) {
		// Validate inputs
		if ( ! $query instanceof \WP_Query || empty( $folder ) ) {
			return;
		}

		// Set meta query parameters for folder filtering
		$query->set( 'meta_key', 'nueve4_folder' );
		$query->set( 'meta_value', $folder );
		$query->set( 'meta_compare', '=' );
	}

	/**
	 * Validate folder ID format
	 *
	 * Checks if a folder ID has a valid format and contains
	 * only allowed characters.
	 *
	 * @since 3.0.0
	 * @param string $folder_id The folder ID to validate
	 * @return bool True if valid, false otherwise
	 */
	private function is_valid_folder_id( $folder_id ) {
		// Allow root folder
		if ( 'root' === $folder_id ) {
			return true;
		}

		// Check for valid folder ID format (alphanumeric, underscore, hyphen)
		if ( ! preg_match( '/^[a-zA-Z0-9_-]+$/', $folder_id ) ) {
			return false;
		}

		// Check reasonable length limits
		if ( strlen( $folder_id ) > 50 ) {
			return false;
		}

		return true;
	}

	/**
	 * Get attachments by folder
	 *
	 * Utility method to retrieve all attachments in a specific folder.
	 *
	 * @since 3.0.0
	 * @param string $folder_id The folder ID to query
	 * @param array  $args      Additional query arguments
	 * @return WP_Query Query object with folder-filtered results
	 */
	public function get_attachments_by_folder( $folder_id, $args = array() ) {
		// Validate folder ID
		if ( ! $this->is_valid_folder_id( $folder_id ) ) {
			return new \WP_Query();
		}

		// Default query arguments
		$default_args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => -1,
			'meta_key'       => 'nueve4_folder',
			'meta_value'     => $folder_id,
			'meta_compare'   => '=',
		);

		// Merge with provided arguments
		$query_args = wp_parse_args( $args, $default_args );

		return new \WP_Query( $query_args );
	}
}