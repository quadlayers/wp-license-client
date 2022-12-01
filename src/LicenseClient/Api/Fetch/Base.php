<?php

namespace LicenseClient\Api\Fetch;

use LicenseClient\Api\Fetch\FetchInterface;

use LicenseClient\Models\Plugin as Model_Plugin;

/**
 * Abstract Base Class
 *
 * Implemented by fetch classes.
 *
 * @since  1.0.0
 */
abstract class Base implements FetchInterface {

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
			function( $value ) {

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

		$query = http_build_query( $args );

		$response = wp_remote_request(
			$api_url . '?' . $query . '&product_key=' . $this->plugin->get_product_key(),
			array(
				'user-agent' => sprintf( 'WLM/%s/%s; %s', $this->plugin->get_plugin_slug(), $this->plugin->get_plugin_version(), $this->plugin->get_activation_site() ),
				'method'     => static::get_rest_method(),
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

		$data = (object) $response;

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
		$is_error = ( ! $response || isset( $response->code ) ) ? true : false;
		if ( $is_error ) {
			return (object) array(
				'error'   => isset( $response->code ) ? $response->code : 404,
				'message' => isset( $response->message ) ? $response->message : __ql_translate( 'Unknown error. Please try again.' ),
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
}
