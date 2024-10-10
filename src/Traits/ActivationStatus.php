<?php

namespace QuadLayers\WP_License_Client\Traits;

trait ActivationStatus {

	public function status() {

		$activation = $this->get();

		return $this->get_status( $activation );
	}

	public function get_status( $activation ) {

		if ( ! isset( $activation['license_key'], $activation['activation_instance'], $activation['license_expiration'] ) ) {
			return 'none';
		}

		if ( ! $this->is_expired_updates( $activation ) ) {
			return 'valid';
		}

		return 'expired';
	}

	public function is_expired( $activation ) {

		if ( $activation['license_expiration'] === '0000-00-00 00:00:00' ) {
			return false;
		}

		return strtotime( current_time( 'mysql' ) ) > strtotime( $activation['license_expiration'] );
	}

	public function is_expired_updates( $activation ) {

		if ( ! $activation['license_updates'] ) {
			return false;
		}

		if ( ! $this->is_expired( $activation ) ) {
			return false;
		}

		return true;
	}
}
