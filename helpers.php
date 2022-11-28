<?php

if ( ! function_exists( '__ql_translate' ) ) {
	function __ql_translate( $text ) {
		$fn         = 'translate';
		$textdomain = 'wdd';
		return $fn( $text, $textdomain );
	}
}

if ( ! function_exists( 'ql_license_client' ) ) {
	function ql_license_client( array $plugin_data ) {
		return new QUADLAYERS\LicenseClient\Load( $plugin_data );
	}
}
