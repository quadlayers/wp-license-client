<?php

if ( ! function_exists( '__ql_translate' ) ) {
	function __ql_translate( $text ) {
		$fn         = 'translate';
		$textdomain = 'qlwlm';
		return $fn( $text, $textdomain );
	}
}

if ( ! function_exists( 'ql_license_client' ) ) {
	function ql_license_client( array $client_data ) {
		$client = new QUADLAYERS\LicenseClient\Load( $client_data );
		return $client;
	}
}
