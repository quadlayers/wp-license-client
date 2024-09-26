<?php
namespace QuadLayers\WP_License_Client\Backend\Page;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;
use QuadLayers\WP_License_Client\Models\Activation as Model_Activation;
use QuadLayers\WP_License_Client\Models\UserData as Model_User_Data;
use QuadLayers\WP_License_Client\Api\Fetch\Activation\Create as API_Fetch_Activation_Create;
use QuadLayers\WP_License_Client\Api\Fetch\Activation\Delete as API_Fetch_Activation_Delete;

/**
 * Controller_Page Class
 */
class Load {

	protected $plugin;
	protected $activation;
	protected $user_data;

	public function __construct( Model_Plugin $model_plugin, Model_Activation $model_activation, Model_User_Data $model_user_data = null ) {
		$this->plugin     = $model_plugin;
		$this->activation = $model_activation;
		$this->user_data  = $model_user_data;

		/**
		 * Don't load plugin menu if parent_menu_slug is set to false
		 */
		if ( false === $this->plugin->get_parent_menu_slug() ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'create_activation' ) );
		add_action( 'admin_init', array( $this, 'delete_activation' ) );
		add_action( 'admin_menu', array( $this, 'add_menu' ), 999 );
	}

	public function add_menu() {

		global $_parent_pages;

		$parent_menu_slug  = $this->plugin->get_parent_menu_slug();
		$menu_slug_license = $this->plugin->get_license_menu_slug();

		if ( ! $menu_slug_license ) {
			return;
		}

		if ( ! isset( $_parent_pages[ $parent_menu_slug ] ) ) {
			$plugin_name = $this->plugin->get_name();
			if ( $plugin_name ) {
				add_menu_page(
					$plugin_name,
					$plugin_name,
					'edit_posts',
					$menu_slug_license,
					'__return_null',
					'dashicons-cloud-upload'
				);
			}
		}

		add_submenu_page(
			$parent_menu_slug,
			esc_html__( 'License', 'wp-license-client' ),
			esc_html__( 'License', 'wp-license-client' ),
			'manage_options',
			$menu_slug_license,
			function () {
				$plugin_slug           = $this->plugin->get_slug();
				$activation            = $this->activation->get();
				$user_data             = $this->user_data->get();
				$activation_delete_url = $this->plugin->get_activation_delete_url();
				include __DIR__ . '/view/license.php';
			},
			99
		);
	}

	public function create_activation() {

		$plugin_slug = $this->plugin->get_slug();

		/**
		 * Validate current page
		 */
		if ( ! isset( $_REQUEST['option_page'] ) || $_REQUEST['option_page'] !== $plugin_slug . '-qlwlm-create' ) {
			return;
		}

		/**
		 * Validate license
		 */
		if ( ! isset( $_REQUEST[ $plugin_slug ] ) ) {
			return;
		}

		/**
		 * Validate nonce
		 */
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), $plugin_slug . '-qlwlm-create-options' ) ) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		$license = wp_unslash( $_REQUEST[ $plugin_slug ] ); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$this->user_data->create( $license );

		$activation = ( new API_Fetch_Activation_Create( $this->plugin ) )->get_data(
			array_merge(
				(array) $license,
				array(
					'activation_site' => $this->plugin->get_activation_site(),
				)
			)
		);

		if ( $activation ) {
			$this->activation->create( (array) $activation );
		}

		wp_clean_plugins_cache();
	}

	public function delete_activation() {

		$plugin_slug = $this->plugin->get_slug();

		/**
		 * Validate current page
		 */
		if ( ! isset( $_REQUEST['option_page'] ) || $_REQUEST['option_page'] !== $plugin_slug . '-qlwlm-delete' ) {
			return;
		}

		/**
		 * Validate nonce
		 */
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), $plugin_slug . '-qlwlm-delete-options' ) ) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		$this->user_data->delete();

		$activation = $this->activation->get();

		$delete = ( new API_Fetch_Activation_Delete( $this->plugin ) )->get_data(
			array(
				'license_key'         => isset( $activation['license_key'] ) ? $activation['license_key'] : null,
				'activation_instance' => isset( $activation['activation_instance'] ) ? $activation['activation_instance'] : null,
			)
		);

		if ( $delete ) {
			$this->activation->delete();
		}
	}
}
