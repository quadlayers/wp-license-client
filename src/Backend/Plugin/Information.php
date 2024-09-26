<?php

namespace QuadLayers\WP_License_Client\Backend\Plugin;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;
use QuadLayers\WP_License_Client\Models\Activation as Model_Activation;
use QuadLayers\WP_License_Client\Api\Fetch\Product\Information as API_Fetch_Product_Information;

/**
 * Controller_Plugin_Information Class
 *
 * Implement plugin information.
 *
 * @since 1.0.0
 */
class Information {

	/**
	 * Instantiated Model_Plugin in the constructor.
	 *
	 * @var Model_Plugin
	 */
	private $plugin;

	/**
	 * Instantiated Model_Activation in the constructor.
	 *
	 * @var Model_Activation
	 */
	private $activation;

	/**
	 * Setup class
	 *
	 * @param Model_Plugin     $plugin
	 * @param Model_Activation $activation
	 */
	public function __construct( Model_Plugin $plugin, Model_Activation $activation ) {

		$this->plugin     = $plugin;
		$this->activation = $activation;

		add_action(
			'admin_init',
			function () {
				add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'add_fetch_data' ) );
			}
		);
	}

	/**
	 * Add fetch data from the server API to the plugin transient.
	 *
	 * @param object $transient
	 * @return object
	 */
	public function add_fetch_data( $transient ) {

		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$product = ( new API_Fetch_Product_Information( $this->plugin ) )->get_data();

		if ( isset( $product->error ) ) {
			return $transient;
		}

		$plugin                 = new \stdClass();
		$plugin->id             = $this->plugin->get_slug();
		$plugin->slug           = $this->plugin->get_slug();
		$plugin->plugin         = $this->plugin->get_base();
		$plugin->new_version    = $product->version;
		$plugin->url            = $product->homepage;
		$plugin->tested         = $product->tested;
		$plugin->upgrade_notice = $product->upgrade_notice;
		$plugin->icons          = array(
			'default' => $product->icon,
		);
		/**
		 * Fields for plugin info
		 */
		$plugin->version         = $product->version;
		$plugin->homepage        = $product->homepage;
		$plugin->name            = $product->name;
		$plugin->author          = $product->author;
		$plugin->requires        = $product->requires;
		$plugin->rating          = 100;
		$plugin->num_ratings     = 5;
		$plugin->active_installs = 10000;
		$plugin->last_updated    = $product->last_updated;
		$plugin->added           = $product->added;
		$plugin->sections        = array(
			'description' => preg_replace( '/<h2(.*?)<\/h2>/si', '<h3"$1</h3>', $product->description ),
			'changelog'   => wpautop( $product->changelog ),
			'screenshots' => $product->screenshots,
		);
		$plugin->donate_link     = $this->plugin->get_url();
		$plugin->banners         = array(
			'low'  => $product->banner_low,
			'high' => $product->banner_high,
		);

		$transient->no_update[ $this->plugin->get_base() ] = $plugin;

		return $transient;
	}
}
