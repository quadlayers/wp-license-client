<?php

namespace QuadLayers\LicenseClient;

require_once __DIR__ . '/../vendor/autoload.php';

use QuadLayers\LicenseClient\Backend\Plugin\Information as Controller_Plugin_Information;
use QuadLayers\LicenseClient\Backend\Plugin\Download as Controller_Plugin_Download;
use QuadLayers\LicenseClient\Backend\Plugin\Table as Controller_Plugin_Table;
use QuadLayers\LicenseClient\Backend\Page\Load as Controller_Page;
use QuadLayers\LicenseClient\Api\Rest\RoutesLibrary as API_Rest_Routes_Library;

use QuadLayers\LicenseClient\Models\Plugin as Model_Plugin;
use QuadLayers\LicenseClient\Models\UserData as Model_User_Data;
use QuadLayers\LicenseClient\Models\Activation as Model_Activation;

/**
 * Class Load
 *
 * @package QuadLayers\LicenseClient\Load
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
		new Controller_Plugin_Download( $this->plugin, $this->activation, $this->user_data );
		new Controller_Plugin_Table( $this->plugin, $this->activation, $this->user_data );
		new Controller_Page( $this->plugin, $this->activation, $this->user_data );

	}
}
