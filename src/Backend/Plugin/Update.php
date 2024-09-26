<?php

namespace QuadLayers\WP_License_Client\Backend\Plugin;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;
use QuadLayers\WP_License_Client\Models\Activation as Model_Activation;
use QuadLayers\WP_License_Client\Api\Fetch\Product\Update as API_Fetch_Product_Update;

/**
 * Controller_Plugin_Update Class
 *
 * Implement plugin automatic updates.
 *
 * @since 1.0.2
 */
class Update {

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

		if ( empty( $transient->no_update[ $this->plugin->get_base() ] ) ) {
			return $transient;
		}

		$plugin = $transient->no_update[ $this->plugin->get_base() ];

		$plugin->package = null;

		/**
		 * Check if there is higher version available.
		 */

		$is_higher_version = version_compare( $plugin->version, $this->plugin->get_version(), '>' );

		if ( ! $is_higher_version ) {
			return $transient;
		}

		/**
		 * Get the license activation data.
		 */

		$activation = $this->activation->get();

		/**
		 * Check if the license is activated. If not, show a notice.
		 */
		if ( ! isset( $activation['license_key'], $activation['activation_instance'] ) ) {
			$plugin->upgrade_notice = sprintf(
				'</p></div><span class="notice notice-error notice-alt" style="display:block; padding: 10px;"><b>%s</b> %s</span>',
				esc_html__( 'Activate your license.', 'wp-license-client' ),
				sprintf(
					esc_html__( 'Please visit %1$s to activate the license or %2$s in our website.', 'wp-license-client' ),
					sprintf(
						'<a href="%s" target="_blank">%s</a>',
						esc_url( $this->plugin->get_menu_license_url() ),
						esc_html__( 'settings', 'wp-license-client' )
					),
					sprintf(
						'<a href="%s" target="_blank">%s</a>',
						esc_url( $this->plugin->get_url() ),
						esc_html__( 'purchase', 'wp-license-client' )
					)
				)
			);
			/**
			 * Set the download link true to display the notice.
			 */
			$transient->response[ $this->plugin->get_base() ] = $plugin;
			return $transient;
		}

		/**
		 * Fetch the download link from the server API.
		 */
		$update_link = ( new API_Fetch_Product_Update( $this->plugin ) )->get_data(
			array(
				'license_key'         => isset( $activation['license_key'] ) ? $activation['license_key'] : null,
				'activation_instance' => isset( $activation['activation_instance'] ) ? $activation['activation_instance'] : null,
			)
		);

		/**
		 * Check if there is an error. If yes, show a notice.
		 */
		if ( isset( $update_link->error ) || filter_var( $update_link, FILTER_VALIDATE_URL ) === false ) {
			$plugin->upgrade_notice                           = esc_html__( 'Automatic updates are currently disabled. Please ensure your license is valid and activated to enable updates.', 'wp-license-client' );
			$transient->response[ $this->plugin->get_base() ] = $plugin;
			return $transient;
		}

		/**
		 * Check if the user has the permission to update plugins. If not, show a notice.
		 */
		if ( ! current_user_can( 'update_plugins' ) ) {
			return $transient;
		}

		$plugin->package       = $update_link;
		$plugin->download_link = $update_link;

		$transient->response[ $this->plugin->get_base() ] = $plugin;

		return $transient;
	}
}
