<?php

namespace QuadLayers\WP_License_Client\Api\Rest;

use QuadLayers\WP_License_Client\Api\Rest\Endpoints\Activation\Create as API_Rest_Activation_License_Create;
use QuadLayers\WP_License_Client\Api\Rest\Endpoints\Activation\Get as API_Rest_Activation_License_Get;
use QuadLayers\WP_License_Client\Api\Rest\Endpoints\Activation\Delete as API_Rest_Activation_License_Delete;

use QuadLayers\WP_License_Client\Api\Rest\Endpoints\UserData\Create as API_Rest_User_Data_Create;
use QuadLayers\WP_License_Client\Api\Rest\Endpoints\UserData\Get as API_Rest_User_Data_Get;
use QuadLayers\WP_License_Client\Api\Rest\Endpoints\UserData\Delete as API_Rest_User_Data_Delete;

use QuadLayers\WP_License_Client\Api\Rest\Endpoints\RouteInterface;


/**
 * API_Rest_Routes Class
 */
class RoutesLibrary {

	protected $routes = array();

	/**
	 * Client data initialized in the constructor.
	 *
	 * @var array
	 */
	private $client_data;

	public function __construct( array $client_data ) {

		$this->client_data = $client_data;

		/**
		 * Don't load rest routes without rest_namespace
		 */
		if ( ! $this->get_rest_namespace() ) {
			return;
		}

		/**
		 * Activation routes
		 */
		new API_Rest_Activation_License_Create( $this->client_data, $this );
		new API_Rest_Activation_License_Get( $this->client_data, $this );
		new API_Rest_Activation_License_Delete( $this->client_data, $this );
		/**
		* User data routes
		*/
		new API_Rest_User_Data_Create( $this->client_data, $this );
		new API_Rest_User_Data_Get( $this->client_data, $this );
		new API_Rest_User_Data_Delete( $this->client_data, $this );
	}

	/**
	 * Get rest namespace from client data
	 *
	 * @return string
	 */
	public function get_rest_namespace() {
		if ( ! isset( $this->client_data['rest_namespace'] ) || ! is_string( $this->client_data['rest_namespace'] ) ) {
			return false;
		}

		return 'ql/wlm/' . $this->client_data['rest_namespace'];
	}

	/**
	 * Register rest routes to allow access to them via $client->routes->get().
	 *
	 * @param RouteInterface $rest_route_instance
	 * @return array|null
	 */
	public function register( RouteInterface $rest_route_instance ) {

		$rest_route = $rest_route_instance->get_rest_route();

		if ( ! isset( $this->routes[ $rest_route ] ) ) {
			$this->routes[ $rest_route ] = $rest_route_instance;
			return;
		}
	}

	/**
	 * Get specific rest route or all of them.
	 *
	 * @param string $rest_path Rest path to get.
	 * @return string|array
	 */
	public function get( $rest_path = null ) {

		if ( null === $rest_path ) {
			return $this->routes;
		}

		if ( isset( $this->routes[ $rest_path ] ) ) {
			return $this->routes[ $rest_path ];
		}
	}
}
