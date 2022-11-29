<?php

namespace QUADLAYERS\LicenseClient\Backend\Plugin;

use QUADLAYERS\LicenseClient\Models\Plugin as Model_Plugin;
use QUADLAYERS\LicenseClient\Models\Activation as Model_Activation;
use QUADLAYERS\LicenseClient\Api\Fetch\Product\Information as API_Fetch_Product_Information;

/**
 * Controller_Plugin_Information Class
 *
 * Implement plugin version notification and automatic updates.
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
	 * @var Model_Plugin
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
			function() {
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

		$fetch = new API_Fetch_Product_Information( $this->plugin );

		$activation = $this->activation->get();

		$product = $fetch->get_data(
			array(
				'license_key'         => isset( $activation['license_key'] ) ? $activation['license_key'] : null,
				'activation_instance' => isset( $activation['activation_instance'] ) ? $activation['activation_instance'] : null,
			)
		);

		if ( isset( $product->error ) ) {
			return $transient;
		}

		$plugin                 = new \stdClass();
		$plugin->id             = $this->plugin->get_plugin_slug();
		$plugin->slug           = $this->plugin->get_plugin_slug();
		$plugin->plugin         = $this->plugin->get_plugin_base();
		$plugin->new_version    = $product->version;
		$plugin->url            = $product->homepage;
		$plugin->tested         = $product->tested;
		$plugin->upgrade_notice = $product->upgrade_notice;
		$plugin->icons          = array( 'default' => $product->icon );
		// Fields for plugin info
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
		$plugin->donate_link     = $this->plugin->get_plugin_url();
		$plugin->banners         = array(
			'low'  => $product->banner_low,
			'high' => $product->banner_high,
		);
		$plugin->package         = null;

		$is_higher_version = version_compare( $product->version, $this->plugin->get_plugin_version(), '>' );

		if ( $is_higher_version ) {

			if ( current_user_can( 'update_plugins' ) && filter_var( $product->download_link, FILTER_VALIDATE_URL ) !== false ) {
				$plugin->package       = $product->download_link;
				$plugin->download_link = $product->download_link;
			}

			$transient->response[ $this->plugin->get_plugin_base() ] = $plugin;
		}

		$transient->no_update[ $this->plugin->get_plugin_base() ] = $plugin;

		return $transient;
	}

}
