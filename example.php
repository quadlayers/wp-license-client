<?php
/**
 * QuadLayers WP Dashboard Widget News
 *
 * Example of how to integrate WooCommerce License Manager Client with your plugin.
 * Include this file in the root folder of your WordPress plugn and setup settings based on your product
 * Documentation: https://quadlayers.com/documentation/woocommerce-license-manager/developer/
 *
 * @package   quadlayers/wp-license-client
 * @link      https://github.com/quadlayers/wp-license-client
 */

if ( ! function_exists( 'your_plugin_license_client_integration' ) ) {

	function your_plugin_license_client_integration() {

		global $your_plugin_license_client;

		if ( ! isset( $your_plugin_license_client ) ) {

			/**
			 * Path to the license client folder.
			 * This is not required if you're using Composer in your package.
			 */
			require_once 'vendor/quadlayers/license-client/index.php';

			$your_plugin_license_client = ql_license_client(
				array(
					'api_url'     => 'https://yoursite.com/wp-json/wc/wlm/',
					'product_key' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
					'plugin_file' => __FILE__,
					// 'rest_namespace' => 'your_plugin', /** Enable rest api */
					// 'parent_menu_slug' => 'your_plugin_menu_slug',
					// 'license_url' => admin_url( 'admin.php?page=your_plugin_license_page' ) ),
				)
			);
		}

		return $your_plugin_license_client;
	}

	your_plugin_license_client_integration();
}
