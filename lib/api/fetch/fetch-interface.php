<?php

namespace QUADLAYERS\LicenseClient\Api\Fetch;

interface FetchInterface {

	public function get_data( array $args);

	public function get_response( array $args);

	public function response_to_data( $response);

	public function get_url();

	public function handle_response();

	public function handle_error();

}
