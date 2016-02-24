<?php

/**
 * Check if `$obj` is a instanceof `Papi_Taxonomy_Type`.
 *
 * @param  mixed $obj
 *
 * @return bool
 */
function papi_is_taxonomy_type( $obj ) {
	return $obj instanceof Papi_Taxonomy_Type;
}

/**
 * Load the entry type id on a post types.
 *
 * @param  string $entry_type_id
 *
 * @return string
 */
function papi_load_taxonomy_type_id( $entry_type_id = '' ) {
	$key       = papi_get_page_type_key();
	$term_id   = papi_get_term_id();
	$taxonomy = papi_get_taxonomy( $term_id );

	// If we have a term id we can load the entry type id
	// from the term.
	if ( $term_id > 0 ) {
		$meta_value      = get_term_meta( $term_id, $key, true );
		$entry_type_id = empty( $meta_value ) ? '' : $meta_value;
	}

	// Load entry type id from the container if it exists.
	if ( empty( $entry_type_id ) ) {
		$key = sprintf( 'entry_type_id.taxonomy.%s', $taxonomy );

		if ( papi()->exists( $key )  ) {
			return papi()->make( $key );
		}
	}

	return $entry_type_id;
}

add_filter( 'papi/entry_type_id', 'papi_load_taxonomy_type_id' );

/**
 * Check if it's a taxonomy page url.
 *
 * @return bool
 */
function papi_is_taxonomy_page() {
	$request_uri = $_SERVER['REQUEST_URI'];
	$parsed_url  = parse_url( $request_uri );

	if ( ! isset( $parsed_url['query'] ) || empty( $parsed_url['query'] ) ) {
		return false;
	}

	$query = $parsed_url['query'];

	return is_admin() && preg_match( '/taxonomy=/', $query );
}
