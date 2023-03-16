<?php
namespace QuadLayers\WP_License_Client\Backend\Notice;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;
use QuadLayers\WP_License_Client\Models\Activation as Model_Activation;
use QuadLayers\WP_License_Client\Models\UserData as Model_User_Data;
use QuadLayers\WP_License_Client\Api\Fetch\Activation\Create as API_Fetch_Activation_Create;
use QuadLayers\WP_License_Client\Api\Fetch\Activation\Delete as API_Fetch_Activation_Delete;

/**
 * Controller_Notice Class
 */
class Load {

	protected $plugin;
	protected $activation;
	protected $user_data;

	public function __construct( Model_Plugin $model_plugin, Model_Activation $model_activation, Model_User_Data $model_user_data = null ) {
		$this->plugin     = $model_plugin;
		$this->activation = $model_activation;
		$this->user_data  = $model_user_data;

		add_action( 'admin_notices', array( $this, 'add_notices' ) );

	}

	public function add_notices() {

		$activation = $this->activation->get();

		if ( ! empty( $activation['license_key'] ) ) {
			return;
		}

		$license_menu_url = $this->plugin->get_menu_license_url();

		if ( ! $license_menu_url ) {
			return;
		}

		?>
		<div class="notice notice-error" data-notice_id="quadmenu-user-rating">
			<div class="notice-container" style="padding-top: 10px; padding-bottom: 10px; display: flex; justify-content: left; align-items: center;">
				<div class="notice-content" style="margin-left: 15px;">
					<p>
						<b><?php printf( esc_html__( 'Please activate your %s license key.', 'wp-license-client' ), esc_html( $this->plugin->get_name() ) ); ?></b>
						<br/>
						<?php esc_html_e( 'Please complete the license activation process to receive automatic updates and enable all premium features.', 'wp-license-client' ); ?>
					</p>
					<a href="<?php echo esc_url( $license_menu_url ); ?>" class="button-primary">
						<?php esc_html_e( 'Activate', 'wp-license-client' ); ?>
					</a>
					<?php if ( $this->plugin->get_license_key_url() ) : ?>
						<a href="<?php echo esc_url( $this->plugin->get_license_key_url() ); ?>" class="button-secondary" target="_blank">
							<?php esc_html_e( 'Get license key', 'wp-license-client' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $this->plugin->get_support_url() ) : ?>
						<a href="<?php echo esc_url( $this->plugin->get_support_url() ); ?>"target="_blank">
							<?php esc_html_e( 'Get support', 'wp-license-client' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php

	}

	private static function find_my_menu_item( $menu_slug, $submenu_page_slug = false ) {

		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return false;
		}

		global $menu, $submenu;

		$check_menu = $submenu_page_slug ? $submenu : $menu;

		if ( empty( $check_menu[ $menu_slug ] ) ) {
			return false;
		}

		if ( false === $submenu_page_slug ) {
			return true;
		}

		foreach ( $check_menu[ $menu_slug ] as $i => $arr ) {

			foreach ( $arr as $slug ) {

				if ( $slug === $submenu_page_slug ) {
					return true;
				}
			}
		}

		return false;
	}

}
