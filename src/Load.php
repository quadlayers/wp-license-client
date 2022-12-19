<?php

namespace QuadLayers\WP_License_Client;

require_once __DIR__ . '/../vendor/autoload.php';

use QuadLayers\WP_License_Client\Backend\Plugin\Information as Controller_Plugin_Information;
use QuadLayers\WP_License_Client\Backend\Plugin\Update as Controller_Plugin_Update;
use QuadLayers\WP_License_Client\Backend\Plugin\Table as Controller_Plugin_Table;
use QuadLayers\WP_License_Client\Backend\Page\Load as Controller_Page;
use QuadLayers\WP_License_Client\Api\Rest\RoutesLibrary as API_Rest_Routes_Library;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;
use QuadLayers\WP_License_Client\Models\UserData as Model_User_Data;
use QuadLayers\WP_License_Client\Models\Activation as Model_Activation;

/**
 * Class Load
 *
 * @package QuadLayers\WP_License_Client\Load
 */
final class Load {

	/**
	 * Client data initialized in the constructor.
	 *
	 * @var array
	 */
	public $client_data;

	/**
	 * Registered rest routes in the constructor of API_Rest_Routes_Library.
	 *
	 * @var API_Rest_Routes_Library
	 */
	public $routes;

	/**
	 * Instantiated Model_Plugin in the constructor.
	 *
	 * @var Model_Plugin
	 */
	public $plugin;

	/**
	 * Instantiated Model_Activation in the constructor.
	 *
	 * @var Model_Plugin
	 */
	public $activation;

	/**
	 * Instantiated Model_User_Data in the constructor.
	 *
	 * @var Model_Plugin
	 */
	public $user_data;

	/**
	 * Setup client instance based on client data.
	 *
	 * @param array $client_data Client data.
	 */
	public function __construct( array $client_data ) {

		$this->client_data = $client_data;

		/**
		 * Get plugin file path
		 */
		if ( ! isset( $this->client_data['dir'] ) ) {
			$this->client_data['dir'] = __DIR__;
		}

		/**
		 * Rest API support
		 */
		$this->routes = new API_Rest_Routes_Library( $this->client_data );

		/**
		 * Don't load plugin models outside admin panel
		 */
		if ( ! is_admin() ) {
			return;
		}

		/**
		 * Load plugin models
		 */
		$this->plugin     = new Model_Plugin( $this->client_data );
		$this->activation = new Model_Activation( $this->plugin );
		$this->user_data  = new Model_User_Data( $this->plugin );
		new Controller_Plugin_Information( $this->plugin, $this->activation, $this->user_data );
		new Controller_Plugin_Update( $this->plugin, $this->activation, $this->user_data );
		new Controller_Plugin_Table( $this->plugin, $this->activation, $this->user_data );
		new Controller_Page( $this->plugin, $this->activation, $this->user_data );

	}
}
