<?php

namespace QUADLAYERS\LicenseClient\Api\Rest;

use QUADLAYERS\LicenseClient\Api\Rest\Endpoints\Activation\Create as API_Rest_Create_Activation_License;
use QUADLAYERS\LicenseClient\Api\Rest\Endpoints\Activation\Delete as API_Rest_Delete_Activation_License;

use QUADLAYERS\LicenseClient\Api\Rest\Endpoints\Activation\Get as API_Rest_Get_Activation_License;

use QUADLAYERS\LicenseClient\Api\Rest\Endpoints\UserData\Create as API_Rest_Create_User_Data;
use QUADLAYERS\LicenseClient\Api\Rest\Endpoints\UserData\Get as API_Rest_Get_User_Data;
use QUADLAYERS\LicenseClient\Api\Rest\Endpoints\UserData\Delete as API_Rest_Delete_User_Data;

use QUADLAYERS\LicenseClient\Api\Rest\Endpoints\RouteInterface;
use QUADLAYERS\LicenseClient\Load;


/**
 * API_Rest_Routes Class
 */

class RoutesLibrary {

	protected $routes = array();

	public function __construct( array $plugin_data, Load $load ) {

		$this->plugin_data = $plugin_data;

		/**
		 * Activation routes
		 */
		new API_Rest_Create_Activation_License( $this->plugin_data, $this );
		new API_Rest_Delete_Activation_License( $this->plugin_data, $this );
		new API_Rest_Get_Activation_License( $this->plugin_data, $this );
		/**
		* User data routes
		*/
		new API_Rest_Create_User_Data( $this->plugin_data, $this );
		new API_Rest_Get_User_Data( $this->plugin_data, $this );
		new API_Rest_Delete_User_Data( $this->plugin_data, $this );
	}

	public function get_rest_namespace() {

		if ( ! isset( $this->plugin_data['rest_namespace'] ) || ! is_string( $this->plugin_data['rest_namespace'] ) ) {
			return 'ql/licenseClient/' . Load::get_instance_key();
		}

		return 'ql/licenseClient/' . $this->plugin_data['rest_namespace'];
	}

	public function register( RouteInterface $rest_route_instance ) {

		$rest_route = $rest_route_instance->get_rest_route();

		if ( ! isset( $this->routes[ $rest_route ] ) ) {
			$this->routes[ $rest_route ] = $rest_route_instance;
			return;
		}

	}

	public function get( string $rest_path = null ) {

		if ( null === $rest_path ) {
			return $this->routes;
		}

		if ( isset( $this->routes[ $rest_path ] ) ) {
			return $this->routes[ $rest_path ];
		}
	}

}
