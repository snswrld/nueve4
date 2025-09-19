<?php
/**
 * Folder Manager Class for Nueve4 Theme
 *
 * Handles folder-specific operations for media library organization.
 * Manages attachment folder assignments and folder selection interface.
 *
 * @package Nueve4
 * @subpackage Media\Folder_Manager
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
 * Folder Manager Class
 *
 * Manages folder assignments for media attachments, provides folder
 * selection interface in media edit screens, and handles new uploads.
 *
 * @since 3.0.0
 */
class Folder_Manager {

	/**
	 * Singleton instance
	 *
	 * @var Folder_Manager|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * Ensures only one instance of the Folder_Manager class exists.
	 *
	 * @since 3.0.0
	 * @return Folder_Manager The singleton instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the folder manager
	 *
	 * Registers WordPress hooks for attachment processing and
	 * folder field management in the media library.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function init() {
		// Hook into attachment processing
		add_action( 'add_attachment', array( $this, 'process_new_attachment' ) );
		add_filter( 'attachment_fields_to_edit', array( $this, 'add_folder_field' ), 10, 2 );
		add_filter( 'attachment_fields_to_save', array( $this, 'save_folder_field' ), 10, 2 );
	}

	/**
	 * Process new attachment uploads
	 *
	 * Assigns newly uploaded attachments to the specified folder
	 * or defaults to root folder if none specified.
	 *
	 * @since 3.0.0
	 * @param int $post_id The attachment post ID
	 * @return void
	 */
	public function process_new_attachment( $post_id ) {
		// Validate post ID
		if ( ! $post_id || ! is_numeric( $post_id ) ) {
			return;
		}

		// Get folder from request, default to root
		$folder = 'root';
		if ( isset( $_REQUEST['folder'] ) ) {
			$folder = sanitize_text_field( wp_unslash( $_REQUEST['folder'] ) );
		}

		// Validate folder exists before assignment
		if ( $this->folder_exists( $folder ) ) {
			update_post_meta( $post_id, 'nueve4_folder', $folder );
		} else {
			// Fallback to root if folder doesn't exist
			update_post_meta( $post_id, 'nueve4_folder', 'root' );
		}
	}

	/**
	 * Add folder selection field to attachment edit screen
	 *
	 * Adds a dropdown field for selecting the folder assignment
	 * in the media library attachment edit interface.
	 *
	 * @since 3.0.0
	 * @param array   $form_fields Existing form fields
	 * @param WP_Post $post        The attachment post object
	 * @return array Modified form fields array
	 */
	public function add_folder_field( $form_fields, $post ) {
		// Validate post object
		if ( ! $post || ! isset( $post->ID ) ) {
			return $form_fields;
		}

		// Get available folders and current assignment
		$folders = $this->get_folders_list();
		$current = get_post_meta( $post->ID, 'nueve4_folder', true );

		// Default to root if no assignment exists
		if ( empty( $current ) ) {
			$current = 'root';
		}

		// Add folder selection field
		$form_fields['nueve4_folder'] = array(
			'label' => __( 'Folder', 'nueve4' ),
			'input' => 'html',
			'html'  => $this->get_folder_select_html( $folders, $current, $post->ID ),
			'helps' => __( 'Select a folder for this media item.', 'nueve4' ),
		);

		return $form_fields;
	}

	/**
	 * Save folder field data
	 *
	 * Processes and saves the folder assignment when the attachment
	 * edit form is submitted.
	 *
	 * @since 3.0.0
	 * @param array $post       The attachment post data
	 * @param array $attachment The attachment form data
	 * @return array The post data array
	 */
	public function save_folder_field( $post, $attachment ) {
		// Validate required data
		if ( ! isset( $post['ID'] ) || ! is_array( $attachment ) ) {
			return $post;
		}

		// Process folder assignment if provided
		if ( isset( $attachment['nueve4_folder'] ) ) {
			$folder = sanitize_text_field( $attachment['nueve4_folder'] );
			
			// Validate folder exists before saving
			if ( $this->folder_exists( $folder ) ) {
				update_post_meta( $post['ID'], 'nueve4_folder', $folder );
			}
		}

		return $post;
	}

	/**
	 * Get list of available folders
	 *
	 * Retrieves all folders from the database and ensures
	 * root folder is always available.
	 *
	 * @since 3.0.0
	 * @return array Array of folder data
	 */
	private function get_folders_list() {
		$folders = get_option( 'nueve4_media_folders', array() );
		
		// Ensure root folder is always first in the list
		$root_folder = array(
			'id'   => 'root',
			'name' => __( 'Root', 'nueve4' ),
		);

		// Merge root with existing folders, avoiding duplicates
		$all_folders = array( $root_folder );
		foreach ( $folders as $folder ) {
			if ( isset( $folder['id'] ) && 'root' !== $folder['id'] ) {
				$all_folders[] = $folder;
			}
		}

		return $all_folders;
	}

	/**
	 * Generate folder selection HTML
	 *
	 * Creates the HTML select element for folder selection
	 * with proper escaping and accessibility attributes.
	 *
	 * @since 3.0.0
	 * @param array  $folders Array of available folders
	 * @param string $current Currently selected folder ID
	 * @param int    $post_id The attachment post ID
	 * @return string HTML select element
	 */
	private function get_folder_select_html( $folders, $current, $post_id ) {
		// Validate inputs
		if ( ! is_array( $folders ) || ! $post_id ) {
			return '';
		}

		// Build select element with proper name attribute
		$html = sprintf(
			'<select name="attachments[%d][nueve4_folder]" id="nueve4-folder-%d">',
			intval( $post_id ),
			intval( $post_id )
		);

		// Add options for each folder
		foreach ( $folders as $folder ) {
			if ( ! isset( $folder['id'] ) || ! isset( $folder['name'] ) ) {
				continue;
			}

			$selected = selected( $current, $folder['id'], false );
			$html    .= sprintf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $folder['id'] ),
				$selected,
				esc_html( $folder['name'] )
			);
		}

		$html .= '</select>';

		return $html;
	}

	/**
	 * Check if a folder exists
	 *
	 * Validates that a folder ID exists in the current folder structure.
	 *
	 * @since 3.0.0
	 * @param string $folder_id The folder ID to check
	 * @return bool True if folder exists, false otherwise
	 */
	private function folder_exists( $folder_id ) {
		// Root folder always exists
		if ( 'root' === $folder_id ) {
			return true;
		}

		// Check if folder exists in database
		$folders = $this->get_folders_list();
		foreach ( $folders as $folder ) {
			if ( isset( $folder['id'] ) && $folder['id'] === $folder_id ) {
				return true;
			}
		}

		return false;
	}
}