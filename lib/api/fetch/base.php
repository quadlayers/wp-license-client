<?php

namespace QUADLAYERS\LicenseClient\Api\Fetch;

use QUADLAYERS\LicenseClient\Api\Fetch\FetchInterface;

use QUADLAYERS\LicenseClient\Models\Plugin as Model_Plugin;

abstract class Base implements FetchInterface {

	public function __construct( Model_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	public static function sanitize_url( $url ) {
		$url = preg_replace( '#([^:])//+#', '$1/', $url );
		return $url;
	}

	public function get_data( array $args = array() ) {
		$response = $this->get_response( $args );
		$data     = $this->response_to_data( $response );
		return $data;
	}

	public function get_url() {

		$api_url = $this->plugin->get_api_url();
		$path    = $this->get_rest_path();

		return self::sanitize_url( $api_url . '/' . $path );
	}

	public function get_response( array $args = array() ) {

		$api_url = $this->get_url();

		$query = http_build_query( $args );

		$response = wp_remote_request(
			$api_url . '?' . $query . '&product_key=' . $this->plugin->get_product_key(),
			array(
				'user-agent' => sprintf( 'WDD/%s/%s; %s', $this->plugin->get_plugin_slug(), $this->plugin->get_plugin_version(), $this->plugin->get_activation_site() ),
				'method'     => static::get_rest_method(),
			)
		);

		$response = json_decode( wp_remote_retrieve_body( $response ) );

		return $this->handle_response( $response );

	}

	public function response_to_data( $response ) {

		$data = $response;

		return $data;

	}

	public function handle_response( $response = null ) {
		$response_error = $this->handle_error( $response );
		if ( $response_error ) {
			return $response_error;
		}
		return $response;
	}

	public function handle_error( $response = null ) {
		$is_error = ( ! $response || isset( $response->code ) ) ? true : false;
		if ( $is_error ) {
			return (object) array(
				'error'   => isset( $response->code ) ? $response->code : 404,
				'message' => isset( $response->message ) ? $response->message : __ql_translate( 'Unknown error.' ),
			);
		}
		return false;
	}

	abstract public function get_rest_path();
}