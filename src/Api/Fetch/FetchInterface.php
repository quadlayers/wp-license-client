<?php

namespace QuadLayers\WP_License_Client\Api\Fetch;

interface FetchInterface {

	/**
	 * Fetch the request and process the response to data.
	 *
	 * @param array $args Array of required arguments for the request.
	 * @return array
	 */
	public function get_data( array $args );

	/**
	 * Get the rest route url, fetch the request and handle the response.
	 *
	 * @param array $args Array of required arguments for the request.
	 * @return array
	 */
	public function get_response( array $args );

	/**
	 * Process the response and convert to required data format.
	 *
	 * @param array $response Fetch response.
	 * @return array
	 */
	public function response_to_data( $response );

	/**
	 * Build the server rest route path.
	 *
	 * @return string
	 */
	public function get_url();

	/**
	 * Handle the response to normalize error and success responses.
	 *
	 * @return string
	 */
	public function handle_response();

	/**
	 * Handle the response to normalize error responses.
	 *
	 * @return string
	 */
	public function handle_error();
}
