<?php
namespace QUADLAYERS\LicenseClient\Backend\Page;

use QUADLAYERS\LicenseClient\Models\Plugin as Model_Plugin;
use QUADLAYERS\LicenseClient\Models\Activation as Model_Activation;
use QUADLAYERS\LicenseClient\Models\UserData as Model_User_Data;
use QUADLAYERS\LicenseClient\Api\Fetch\Activation\Create as API_Fetch_Activation_Create;
use QUADLAYERS\LicenseClient\Api\Fetch\Activation\Delete as API_Fetch_Activation_Delete;

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

		add_action(
			'plugins_loaded',
			function() {
				add_action( 'admin_init', array( $this, 'create_activation' ) );
				add_action( 'admin_init', array( $this, 'delete_activation' ) );
				add_action( 'admin_menu', array( $this, 'add_menu' ), 999 );
			},
			99
		);

	}

	function add_menu() {

		global $_parent_pages;

		$parent_menu_slug  = $this->plugin->get_parent_menu_slug();
		$menu_slug_license = $this->plugin->get_license_menu_slug();

		if ( ! $menu_slug_license ) {
			return;
		}

		if ( ! isset( $_parent_pages[ $parent_menu_slug ] ) ) {
			$plugin_name = $this->plugin->get_plugin_name();
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
			__ql_translate( 'License' ),
			__ql_translate( 'License' ),
			'manage_options',
			$menu_slug_license,
			function () {
				$plugin_slug = $this->plugin->get_plugin_slug();
				$activation  = $this->activation->get();
				$user_data   = $this->user_data->get();
				include __DIR__ . '/view/license.php';
			},
			99
		);

	}

	function create_activation() {

		$plugin_slug = $this->plugin->get_plugin_slug();

		if ( ! isset( $_REQUEST['option_page'] ) ) {
			return;
		}

		if ( $_REQUEST['option_page'] !== $plugin_slug . '-qlwlm-create' ) {
			return;
		}

		if ( ! isset( $_REQUEST[ $plugin_slug ] ) ) {
			return;
		}

		$this->user_data->create( $_REQUEST[ $plugin_slug ] );

		$fetch = new API_Fetch_Activation_Create( $this->plugin );

		$activation = $fetch->get_data(
			array_merge(
				(array) $_REQUEST[ $plugin_slug ],
				array(
					'activation_site' => $this->plugin->get_activation_site(),
					'product_key'     => $this->plugin->get_product_key(),
				)
			)
		);

		if ( $activation ) {
			$this->activation->create( (array) $activation );
		}
	}

	function delete_activation() {

		$plugin_slug = $this->plugin->get_plugin_slug();

		if ( ! isset( $_REQUEST['option_page'] ) ) {
			return;
		}

		if ( $_REQUEST['option_page'] !== $plugin_slug . '-qlwlm-delete' ) {
			return;
		}

		$this->user_data->delete();

		$fetch = new API_Fetch_Activation_Delete( $this->plugin );

		$activation = $this->activation->get();

		$delete = $fetch->get_data(
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
