<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Papi Page Type class.
 * All page types in the WordPress theme will extend this class.
 *
 * @package Papi
 * @version 1.0.0
 */

class Papi_Page_Type extends Papi_Page_Type_Meta {

	private $boxes = array();

	/**
	 * Contains all register properties on this page.
	 * Will only contain root level properties.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	private $properties = array();

	/**
	 * Array of post type supports to remove.
	 *
	 * @var array
	 * @since 1.0.0
	 */

	private $post_type_supports = array();

	/**
	 * Load a page type by the file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path
	 * @param bool $register
	 */

	public function __construct( $file_path = '' ) {
		parent::__construct( $file_path );
	}

	/**
	 * Add new meta box with properties.
	 *
	 * @param mixed $file_or_options .
	 * @param array $properties
	 *
	 * @since 1.0.0
	 */

	protected function box( $file_or_options = array(), $properties = array() ) {
		list( $options, $properties ) = _papi_get_options_and_properties( $file_or_options, $properties, true );

		$post_type = _papi_get_wp_post_type();

		// Check so we have a post the to add the box to.
		if ( empty( $page_type ) || ! $this->has_post_type( $post_type ) ) {
			return null;
		}

		// Add post type to the options array.
		$options['post_type'] = $post_type;

		array_push($this->boxes, array($options, $properties));
	}

	/**
	 * This function will setup all meta boxes.
	 *
	 * @since 1.0.0
	 */

	public function setup () {
		if ( ! method_exists( $this, 'register' ) ) {
			return null;
		}

		$this->register();

		foreach( $this->boxes as $box ) {
			new Papi_Admin_Meta_Box( $box[0], $box[1] );
		}
	}

	/**
	 * Add new property to the page using array or rendering property template file.
	 *
	 * @param string|array $file_or_options
	 * @param array $values
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	protected function property( $file_or_options = array(), $values = array() ) {
		return papi_property( $file_or_options, $values );
	}

	/**
	 * Remove post type support. Runs once, on page load.
	 *
	 * @param array $remove_post_type_support
	 *
	 * @since 1.0.0
	 */

	protected function remove( $post_type_supports = array() ) {
		$this->post_type_supports = array_merge( $this->post_type_supports, _papi_to_array( $post_type_supports ) );
		add_action( 'init', array( $this, 'remove_post_type_support' ) );
	}

	/**
	 * Remove post type support action.
	 *
	 * @since 1.0.0
	 */

	public function remove_post_type_support() {
		$post_type = _papi_get_wp_post_type();

		if ( empty( $post_type ) ) {
			return;
		}

		foreach ( $this->post_type_supports as $post_type_support ) {
			remove_post_type_support( $post_type, $post_type_support );
		}
	}

	/**
	 * Add a new tab.
	 *
	 * @param string $title
	 * @param array $options
	 * @param array $properties
	 *
	 * @since 1.0
	 *
	 * @return array
	 */

	protected function tab( $file_or_options = array(), $properties = array() ) {
		return papi_tab( $file_or_options, $properties );
	}

	/**
	 * Load template file.
	 *
	 * @param string $file
	 * @param array $values
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */

	protected function template( $file, $values = array() ) {
		return papi_template( $file, $values );
	}
}
