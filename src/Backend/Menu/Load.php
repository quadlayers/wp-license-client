<?php
namespace QuadLayers\WP_License_Client\Backend\Menu;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;
use QuadLayers\WP_License_Client\Models\Activation as Model_Activation;

/**
 * Controller_Menu Class
 */
class Load {

	protected $plugin;
	protected $activation;
	protected $user_data;

	public function __construct( Model_Plugin $model_plugin, Model_Activation $model_activation ) {
		$this->plugin     = $model_plugin;
		$this->activation = $model_activation;

		add_action( 'admin_footer', array( $this, 'add_menu_alert' ) );
	}

	public function add_menu_alert() {

		global $_parent_pages;

		$parent_menu_slug = $this->plugin->get_parent_menu_slug();

		if ( ! $parent_menu_slug ) {
			return;
		}

		if ( ! isset( $_parent_pages[ $parent_menu_slug ] ) ) {
			return;
		}

		if ( 'valid' === $this->activation->status() ) {
			return;
		}

		// Output inline CSS using the slug dynamically
		echo '<style>
			#adminmenu #toplevel_page_' . esc_attr( $parent_menu_slug ) . ' a[href="admin.php?page=' . esc_attr( $parent_menu_slug ) . '"] .wp-menu-name:before {
				content: "!";
				color: #fff;
				background-color: #d63638;
				padding: 1px 7px;
				font-size: 9px;
				font-weight: 700;
				border-radius: 10px;
				position: absolute;
				right: 15px;
				line-height: 16px;
			}
		</style>';
	}
}
