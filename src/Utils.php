<?php

namespace QuadLayers\WP_License_Client;

final class Utils {

	public static function get_activation_status( $activation ) {

		if ( ! isset( $activation['license_key'], $activation['activation_instance'] ) ) {
			return 'none';
		}

		if ( ! self::is_expired_updates( $activation ) ) {
			return 'valid';
		}

		return 'expired';
	}

	public static function is_expired( $activation ) {

		if ( $activation['license_expiration'] === '0000-00-00 00:00:00' ) {
			return false;
		}

		return strtotime( current_time( 'mysql' ) ) > strtotime( $activation['license_expiration'] );
	}

	public static function is_expired_updates( $activation ) {

		if ( ! $activation['license_updates'] ) {
			return false;
		}

		if ( ! self::is_expired( $activation ) ) {
			return false;
		}

		return true;
	}
}
