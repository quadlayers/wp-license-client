<?php

if ( ! function_exists( 'ql_license_client' ) ) {
	function ql_license_client( array $client_data ) {
		$client = new QuadLayers\WP_License_Client\Load( $client_data );
		return $client;
	}
}
