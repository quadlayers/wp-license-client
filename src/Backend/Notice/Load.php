<?php
namespace QuadLayers\WP_License_Client\Backend\Notice;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;
use QuadLayers\WP_License_Client\Models\Activation as Model_Activation;
use QuadLayers\WP_License_Client\Models\UserData as Model_User_Data;

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

		add_action( 'admin_notices', array( $this, 'add_license_activate' ) );
		add_action( 'admin_notices', array( $this, 'add_license_expired' ) );
	}

	public function add_license_activate() {

		if ( 'none' !== $this->activation->status() ) {
			return;
		}

		if ( ! $this->plugin->get_menu_license_url() ) {
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
					<span style="display:flex;align-items:center;gap: 15px;">
						<a href="<?php echo esc_url( $this->plugin->get_menu_license_url() ); ?>" class="button-primary">
							<?php esc_html_e( 'Activate', 'wp-license-client' ); ?>
						</a>
						<a href="<?php echo esc_url( $this->plugin->get_url() ); ?>" target="_blank" class="button-secondary">
							<?php esc_html_e( 'Purchase', 'wp-license-client' ); ?>
						</a>
						<?php if ( $this->plugin->get_license_key_url() ) : ?>
							<a href="<?php echo esc_url( $this->plugin->get_license_key_url() ); ?>" target="_blank">
								<?php esc_html_e( 'Get license key', 'wp-license-client' ); ?>
							</a>
						<?php endif; ?>
						<?php if ( $this->plugin->get_support_url() ) : ?>
							<a href="<?php echo esc_url( $this->plugin->get_support_url() ); ?>" target="_blank">
								<?php esc_html_e( 'Get support', 'wp-license-client' ); ?>
							</a>
						<?php endif; ?>
					</span>
				</div>
			</div>
		</div>
		<?php
	}

	public function add_license_expired() {

		if ( 'expired' !== $this->activation->status() ) {
			return;
		}

		if ( ! $this->plugin->get_menu_license_url() ) {
			return;
		}

		$activation = $this->activation->get();

		$user = wp_get_current_user();

		?>
		<div class="notice notice-error" data-notice_id="quadmenu-user-rating">
			<div class="notice-container" style="padding-top: 10px; padding-bottom: 10px; display: flex; justify-content: left; align-items: center;">
				<div class="notice-content" style="margin-left: 15px;">
					<p>
						<b><?php printf( esc_html__( 'Your %s license has expired.', 'wp-license-client' ), esc_html( $this->plugin->get_name() ) ); ?></b>
						<br/>
						<?php printf( esc_html__( 'Hello %1$s, your license has expired. You can still access premium features for 7 more days. Renew now to avoid losing them.', 'wp-license-client' ), esc_html( $user->display_name ), esc_html( $this->plugin->get_name() ), esc_html( $activation['license_expiration'] ) ); ?>
					</p>
					<span style="display:flex;align-items:center;gap: 15px;">
						<a href="<?php echo esc_url( $this->plugin->get_license_key_url() ); ?>" class="button-secondary">
							<?php esc_html_e( 'Renew License', 'wp-license-client' ); ?>
						</a>
						<?php if ( $this->plugin->get_support_url() ) : ?>
							<a href="<?php echo esc_url( $this->plugin->get_support_url() ); ?>" target="_blank">
								<?php esc_html_e( 'Get support', 'wp-license-client' ); ?>
							</a>
						<?php endif; ?>
					</span>
				</div>
			</div>
		</div>
		<?php
	}
}
