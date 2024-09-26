<?php

namespace QuadLayers\WP_License_Client\Api\Fetch;

use QuadLayers\WP_License_Client\Api\Fetch\FetchInterface;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;

/**
 * Abstract Base Class
 *
 * Implemented by fetch classes.
 *
 * @since  1.0.0
 */
abstract class Base implements FetchInterface {

	/**
	 * Plugin model
	 *
	 * @var Model_Plugin
	 */
	protected $plugin;

	/**
	 * Setup class with plugin model.
	 *
	 * @var Model_Plugin
	 */
	public function __construct( Model_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Sanitize the server API url.
	 *
	 * @return string
	 */
	public static function sanitize_url( $url ) {
		$url = preg_replace( '#([^:])//+#', '$1/', $url );
		return $url;
	}

	/**
	 * Build the url, fetch the request and handle the response.
	 *
	 * @param array $args Array of required arguments for the request.
	 * @return object
	 */
	public function get_data( array $args = array() ) {

		/**
		 * Sanitize args
		 * Trim and remove line breaks
		 */
		$args = array_map(
			function ( $value ) {

				$value = trim( $value );
				$value = preg_replace( "/[\r\n|\n|\r]+/", PHP_EOL, $value );

				return $value;
			},
			$args
		);

		$response = $this->get_response( $args );
		$data     = $this->response_to_data( $response );
		return $data;
	}

	/**
	 * Build the server rest route path.
	 *
	 * @return string
	 */
	public function get_url() {

		$api_url = $this->plugin->get_api_url();
		$path    = $this->get_rest_path();

		return self::sanitize_url( $api_url . '/' . $path );
	}

	/**
	 * Get the rest route url, fetch the request and handle the response.
	 *
	 * @param array $args Array of required arguments for the request.
	 * @return array
	 */
	public function get_response( array $args = array() ) {

		$api_url = $this->get_url();

		$query = http_build_query( array_merge( $args, array( 'product_key' => $this->plugin->get_product_key() ) ) );

		$response = wp_remote_request(
			$api_url . '?' . $query,
			array(
				// 'user-agent' => sprintf( 'WLM/%s/%s; %s', $this->plugin->get_slug(), $this->plugin->get_version(), $this->plugin->get_activation_site() ),
				'method'  => static::get_rest_method(),
				'timeout' => 100,
			)
		);

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		return $this->handle_response( $response );
	}

	/**
	 * Process the response and convert to required data format.
	 *
	 * @param array $response Fetch response.
	 * @return object
	 */
	public function response_to_data( $response ) {

		$data = is_array( $response ) ? (object) $response : $response;

		return $data;
	}

	/**
	 * Handle the response to normalize error and success responses.
	 *
	 * @return string
	 */
	public function handle_response( $response = null ) {
		$response_error = $this->handle_error( $response );
		if ( $response_error ) {
			return $response_error;
		}
		return $response;
	}

	/**
	 * Handle the response to normalize error responses.
	 *
	 * @return string
	 */
	public function handle_error( $response = null ) {
		$is_error = ( ! $response || isset( $response->code ) || isset( $response->error ) ) ? true : false;
		if ( $is_error ) {
			return (object) array(
				'error'   => isset( $response->code ) ? $response->code : ( isset( $response->error ) ? $response->error : 404 ),
				'message' => isset( $response->message ) ? $response->message : esc_html__( 'Unknown error. Please try again.', 'wp-license-client' ),
			);
		}
		return false;
	}

	/**
	 * Get rest route path
	 *
	 * @return string
	 */
	abstract public function get_rest_path();

	/**
	 * Get rest method
	 *
	 * @return string POST
	 */
	abstract public static function get_rest_method();
}
