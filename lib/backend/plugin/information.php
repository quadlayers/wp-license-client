<?php

namespace QUADLAYERS\LicenseClient\Backend\Plugin;

use QUADLAYERS\LicenseClient\Models\Plugin as Model_Plugin;
use QUADLAYERS\LicenseClient\Api\Fetch\Product\Information as API_Fetch_Product_Information;

/**
 * Controller_Plugin_Information Class
 */

class Information {

	private $plugin;

	public function __construct( Model_Plugin $plugin ) {

		$this->plugin = $plugin;

		add_action(
			'admin_init',
			function() {
				add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'add_fetch_data' ) );
			}
		);
	}

	public function add_fetch_data( $transient ) {

		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$fetch = new API_Fetch_Product_Information( $this->plugin );

		$data = $fetch->get_data();

		if ( isset( $data->error ) ) {
			return $transient;
		}

		$plugin                 = new \stdClass();
		$plugin->id             = $this->plugin->get_plugin_slug();
		$plugin->slug           = $this->plugin->get_plugin_slug();
		$plugin->plugin         = $this->plugin->get_plugin_base();
		$plugin->new_version    = $data->version;
		$plugin->url            = $data->homepage;
		$plugin->tested         = $data->tested;
		$plugin->upgrade_notice = $data->upgrade_notice;
		$plugin->icons          = array( 'default' => $data->icon );
		// Fields for plugin info
		$plugin->version         = $data->version;
		$plugin->homepage        = $data->homepage;
		$plugin->name            = $data->name;
		$plugin->author          = $data->author;
		$plugin->requires        = $data->requires;
		$plugin->rating          = 100;
		$plugin->num_ratings     = 5;
		$plugin->active_installs = 10000;
		$plugin->last_updated    = $data->last_updated;
		$plugin->added           = $data->added;
		$plugin->sections        = array(
			'description' => preg_replace( '/<h2(.*?)<\/h2>/si', '<h3"$1</h3>', $data->description ),
			'changelog'   => wpautop( $data->changelog ),
			'screenshots' => $data->screenshots,
		);
		$plugin->donate_link     = $this->plugin->get_plugin_url();
		$plugin->banners         = array(
			'low'  => $data->banner_low,
			'high' => $data->banner_high,
		);
		$plugin->package         = null;

		$is_higher_version = version_compare( $data->version, $this->plugin->get_plugin_version(), '>' );

		if ( $is_higher_version ) {

			if ( current_user_can( 'update_plugins' ) && filter_var( $data->download_link, FILTER_VALIDATE_URL ) !== false ) {
				$plugin->package       = $data->download_link;
				$plugin->download_link = $data->download_link;
			}

			$transient->response[ $this->plugin->get_plugin_base() ] = $plugin;
		}

		$transient->no_update[ $this->plugin->get_plugin_base() ] = $plugin;

		return $transient;
	}

}
