<?php

namespace QUADLAYERS\LicenseClient;

use QUADLAYERS\LicenseClient\Backend\Plugin\Notification as Controller_Plugin_Notification;
use QUADLAYERS\LicenseClient\Backend\Plugin\Information as Controller_Plugin_Information;
use QUADLAYERS\LicenseClient\Backend\Page\Load as Controller_Page;
use QUADLAYERS\LicenseClient\Api\Rest\RoutesLibrary as API_Rest_Routes_Library;

use QUADLAYERS\LicenseClient\Models\Plugin as Model_Plugin;
use QUADLAYERS\LicenseClient\Models\UserData as Model_User_Data;
use QUADLAYERS\LicenseClient\Models\Activation as Model_Activation;

final class Load {

	public $plugin_data;
	public $routes;
	public $plugin;
	public $activation;
	public $user_data;

	public function __construct( array $plugin_data ) {

		$this->plugin_data = $plugin_data;

		/**
		 * Get plugin file path
		 */

		if ( ! isset( $this->plugin_data['dir'] ) ) {
			$this->plugin_data['dir'] = __DIR__;
		}

		/**
		 * Rest API support
		 */
		$this->routes = new API_Rest_Routes_Library( $this->plugin_data );

		/**
		 * Don't load plugin models outside admin panel
		 */

		if ( ! is_admin() ) {
			return;
		}

		/**
		 * Load plugin models
		 */
		$this->plugin     = new Model_Plugin( $this->plugin_data );
		$this->activation = new Model_Activation( $this->plugin );
		$this->user_data  = new Model_User_Data( $this->plugin );
		new Controller_Plugin_Information( $this->plugin, $this->activation, $this->user_data );
		new Controller_Plugin_Notification( $this->plugin, $this->activation, $this->user_data );
		new Controller_Page( $this->plugin, $this->activation, $this->user_data );

	}
}
