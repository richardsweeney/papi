<?php

/**
 * Papi option functions.
 *
 * @package Papi
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Delete value in the database.
 *
 * @param string $slug
 *
 * @return bool
 */

function papi_delete_option( $slug ) {
	return papi_delete_field( 0, $slug, 'option' );
}

/**
 * Check if it's a option page url.
 *
 * @return bool
 */

function papi_is_option_page() {
	$request_uri = $_SERVER['REQUEST_URI'];
	$parsed_url  = parse_url( $request_uri );

	if ( ! isset( $parsed_url['query'] ) || empty ( $parsed_url['query'] ) ) {
		return false;
	}

	$query = $parsed_url['query'];

	return ! preg_match( '/page\-type\=/', $query ) && preg_match( '/page\=/', $query );
}

/**
 * Get property value for property on a option page.
 *
 * @param string $slug
 * @param mixed $default
 *
 * @return mixed
 */

function papi_option( $slug, $default = null ) {
	return papi_field( 0, $slug, $default, 'option' );
}

/**
 * Shortcode for `papi_option` function.
 *
 * [papi_option slug="field_name" default="Default value"][/papi_option]
 *
 * @param array $atts
 *
 * @return mixed
 */

function papi_option_shortcode( $atts ) {
	$default = isset( $atts['default'] ) ? $atts['default'] : '';

	if ( empty( $atts['slug'] ) ) {
		$value = $default;
	} else {
		$value = papi_option( $atts['slug'], $default );
	}

	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	return $value;
}

add_shortcode( 'papi_option', 'papi_option_shortcode' );

/**
 * Update field with new value. The old value will be deleted.
 *
 * @param string $slug
 * @param string $type
 *
 * @return bool
 */

function papi_update_option( $slug, $value = null ) {
	return papi_update_field( 0, $slug, $value, 'option' );
}

/**
 * Echo the value for property on a option page.
 *
 * @param string $slug
 * @param mixed $default
 */

function the_papi_option( $slug = null, $default = null ) {
	$value = papi_option( $slug, $default );

	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	echo $value;
}
