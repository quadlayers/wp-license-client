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

	private static $_instance = array();
	private static $_instance_key;
	public $routes;
	public $plugin;
	public $activation;
	public $user_data;

	private function __construct( array $plugin_data ) {

		/**
		 * Get plugin file path
		 */
		$plugin_data['dir'] = __DIR__;

		/**
		 * Get rest namespace
		 */
		if ( ! isset( $plugin_data['rest_namespace'] ) ) {
			$plugin_data['rest_namespace'] = self::get_instance_key();
		}

		$this->routes = new API_Rest_Routes_Library( $plugin_data, $this );

		/**
		 * Don't load plugin models outside admin panel
		 */
		if ( ! is_admin() ) {
			return;
		}
		$this->plugin     = new Model_Plugin( $plugin_data );
		$this->activation = new Model_Activation( $this->plugin );
		$this->user_data  = new Model_User_Data( $this->plugin );
		new Controller_Plugin_Information( $this->plugin, $this->activation, $this->user_data );
		new Controller_Plugin_Notification( $this->plugin, $this->activation, $this->user_data );
		new Controller_Page( $this->plugin, $this->activation, $this->user_data );

	}

	public static function get_instance_key( array $plugin_data = null ) {

		if ( self::$_instance_key ) {
			return self::$_instance_key;
		}

		self::$_instance_key = md5( serialize( $plugin_data ) );

		return self::$_instance_key;
	}

	public static function instance( array $plugin_data ) {

		$_instance_key = self::get_instance_key( $plugin_data );

		if ( ! isset( self::$_instance[ $_instance_key ] ) ) {
			self::$_instance[ $_instance_key ] = new self( $plugin_data );
		}

		return self::$_instance[ $_instance_key ];
	}
}
