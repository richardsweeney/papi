<?php

/**
 * Admin class that handle ajax calls.
 */
class Papi_Admin_Ajax {

	/**
	 * The action prefix for Papi ajax actions.
	 *
	 * @var string
	 */
	private $action_prefix = 'papi/ajax/';

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Setup actions.
	 */
	private function setup_actions() {
		add_action( 'init', [$this, 'add_endpoint'] );
		add_action( 'parse_query', [$this, 'handle_papi_ajax'] );
		add_action( 'admin_enqueue_scripts', [$this, 'ajax_url'], 10 );

		// Ajax actions.
		add_action( $this->action_prefix . 'get_property', [$this, 'get_property'] );
		add_action( $this->action_prefix . 'get_properties', [$this, 'get_properties'] );
		add_action( $this->action_prefix . 'get_rules_result', [$this, 'get_rules_result'] );
		add_action( $this->action_prefix . 'get_posts', [$this, 'get_posts'] );
		add_action( $this->action_prefix . 'get_terms', [$this, 'get_terms'] );
	}

	/**
	 * Add ajax endpoint.
	 */
	public function add_endpoint() {
		add_rewrite_tag( '%action%', '([^/]*)' );
		add_rewrite_rule( 'papi-ajax/([^/]*)/?', 'index.php?action=$matches[1]', 'top' );
	}

	/**
	 * Add ajax url to Papi JavaScript object.
	 */
	public function ajax_url() {
		$url = esc_url( trailingslashit( get_bloginfo( 'url' ) ) . 'papi-ajax/' );
		?>
		<script type="text/javascript">
			var papi = papi || {};
			papi.ajaxUrl = '<?php echo $url; ?>';
		</script>
		<?php
	}

	/**
	 * Handle Papi ajax.
	 */
	public function handle_papi_ajax() {
		global $wp_query;

		if ( ! is_object( $wp_query ) ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( ! papi_is_empty( papi_get_qs( 'action' ) ) ) {
			$wp_query->set( 'papi_ajax_action', papi_get_qs( 'action' ) );
		}

		$ajax_action = $wp_query->get( 'papi_ajax_action' );

		if ( is_user_logged_in() && has_action( $this->action_prefix . $ajax_action ) !== false ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}

			if ( ! defined( 'DOING_PAPI_AJAX' ) ) {
				define( 'DOING_PAPI_AJAX', true );
			}

			status_header( 200 );
			do_action( $this->action_prefix . $ajax_action );
			wp_die();
		}
	}

	/**
	 * Get posts via GET.
	 *
	 * Posts with empty title will be ignored.
	 *
	 * GET /papi-ajax/?action=get_posts
	 */
	public function get_posts() {
		$args   = papi_get_qs( 'query' ) ?: [];
		$args   = is_array( $args ) ? $args : [];
		$fields = papi_get_qs( 'fields' ) ?: [];
		$fields = is_array( $fields ) ? $fields : [];
		$query  = new WP_Query( array_merge( [
			'post_type'              => ['post'],
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false
		], $args ) );

		$posts = $query->get_posts();
		$posts = array_filter( $posts, function ( $post ) {
			return ! empty( $post->post_title );
		} );

		// @codeCoverageIgnoreStart
		usort( $posts, function ( $a, $b ) {
			return strcmp( strtolower( $a->post_title ), strtolower( $b->post_title ) );
		} );
		// @codeCoverageIgnoreEnd

		if ( ! empty( $fields ) ) {
			foreach ( $posts as $index => $post ) {
				$item = [];

				foreach ( $fields as $field ) {
					$item[$field] = $post->$field;
				}

				$posts[$index] = $item;
			}
		}

		wp_send_json( $posts );
	}

	/**
	 * Get property html via GET.
	 *
	 * GET /papi-ajax/?action=get_property
	 */
	public function get_property() {
		$default_options = Papi_Core_Property::factory()->get_options();
		$keys            = array_keys( get_object_vars( $default_options ) );
		$options         = papi_get_qs( $keys, true );

		if ( $property = papi_property( $options ) ) {
			ob_start();

			$property->render_ajax_request();

			$html = ob_get_clean();

			wp_send_json( [
				'html' => utf8_encode( $html )
			] );

			return;
		}

		$this->render_error( 'No property found' );
	}

	/**
	 * Get properties via POST.
	 *
	 * POST /papi-ajax/?action=get_properties
	 */
	public function get_properties() {
		if ( ! papi_get_sanitized_post( 'properties' ) ) {
			$this->render_error( 'No properties found' );
			return;
		}

		$items = json_decode(
			stripslashes( $_POST['properties'] ),
			true
		);

		if ( empty( $items ) || ! is_array( $items ) ) {
			$this->render_error( 'No properties found' );
			return;
		}

		foreach ( $items as $key => $item ) {
			$property = papi_property( (array) $item );

			if ( ! papi_is_property( $property ) ) {
				unset( $items[$key] );
				continue;
			}

			ob_start();

			$property->render_ajax_request();

			$items[$key] = trim( ob_get_clean() );
		}

		$items = array_filter( $items );

		if ( empty( $items ) ) {
			$this->render_error( 'No properties found' );

			return;
		}

		wp_send_json( [
			'html' => $items
		] );
	}

	/**
	 * Get rules result via GET.
	 *
	 * GET /papi-ajax/?action=get_rules_result
	 */
	public function get_rules_result() {
		if ( ! papi_get_sanitized_post( 'data' ) ) {
			$this->render_error( 'No rule found' );
			return;
		}

		$data = json_decode(
			stripslashes( papi_get_sanitized_post( 'data' ) ),
			true
		);

		if ( empty( $data ) || ! is_array( $data ) || ! isset( $data['slug'] ) ) {
			$this->render_error( 'No rule found' );
			return;
		}

		$entry_type = papi_get_entry_type_by_meta_id();

		if ( $entry_type instanceof Papi_Entry_Type === false ) {
			$this->render_error( 'No rule found' );
			return;
		}

		if ( preg_match( '/\[\]$/', $data['slug'] ) ) {
			$data['slug'] = preg_replace( '/\[\]$/', '', $data['slug'] );
		}

		if ( $property = $entry_type->get_property( $data['slug'] ) ) {
			wp_send_json( [
				'render' => $property->render_is_allowed_by_rules( $data['rules'] )
			] );

			return;
		}

		$this->render_error( 'No rule found' );
	}

	/**
	 * Get terms via GET.
	 *
	 * GET /papi-ajax/?action=get_terms
	 */
	public function get_terms() {
		$query    = papi_get_qs( 'query' ) ?: [];
		$query    = is_array( $query ) ? $query : [];
		$taxonomy = papi_get_qs( 'taxonomy' ) ?: '';

		$args = array_merge( $query, [
			'fields' => 'id=>name'
		] );

		$terms = [];
		if ( taxonomy_exists( $taxonomy ) ) {
			$terms = get_terms( $taxonomy, $args );
		}

		wp_send_json( $terms );
	}

	/**
	 * Render error message.
	 *
	 * @param string $message
	 */
	public function render_error( $message ) {
		wp_send_json( [
			'error' => $message
		] );
	}
}

new Papi_Admin_Ajax;
