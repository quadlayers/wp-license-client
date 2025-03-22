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
		add_action( 'admin_notices', array( $this, 'add_license_error' ) );
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

	/**
	 * Display license API error messages with detailed explanation about premium features
	 */
	public function add_license_error() {

		if ( 'error' !== $this->activation->status() ) {
			return;
		}

		$activation = $this->activation->get();

		if ( empty( $activation['message'] ) ) {
			return;
		}

		$plugin_name     = $this->plugin->get_name();
		$message         = $activation['message'];
		$license_url     = $this->plugin->get_menu_license_url();
		$license_key_url = $this->plugin->get_license_key_url();

		$is_max_activations = false;
		if ( isset( $activation['error'] ) && $activation['error'] === 2003 ) {
			$is_max_activations = true;
		} elseif ( stripos( $message, 'maximum' ) !== false && stripos( $message, 'activations' ) !== false ) {
			$is_max_activations = true;
		}

		?>
		<div class="notice notice-error" data-notice_id="quadlayers-license-error">
			<div class="notice-container" style="padding-top: 10px; padding-bottom: 10px; display: flex; justify-content: left; align-items: center;">
				<div class="notice-content" style="margin-left: 15px;">
					<p>
						<b><?php printf( esc_html__( '%s license activation error!', 'wp-license-client' ), esc_html( $plugin_name ) ); ?></b>
						<br/>
						<?php echo esc_html( $message ); ?>
					</p>
					<p>
						<strong><?php esc_html_e( 'Important:', 'wp-license-client' ); ?></strong> 
						<?php
						printf(
							esc_html__( 'Premium features for %s will be deactivated because your license is not active. Please resolve this issue to restore full functionality.', 'wp-license-client' ),
							esc_html( $plugin_name )
						);
						?>
					</p>
					<span style="display:flex;align-items:center;gap: 15px;">
						<?php if ( $license_url ) : ?>
							<a href="<?php echo esc_url( $license_url ); ?>" class="button-primary">
								<?php esc_html_e( 'Activate', 'wp-license-client' ); ?>
							</a>
						<?php endif; ?>
						
						<?php if ( $license_key_url ) : ?>
							<a href="<?php echo esc_url( $license_key_url ); ?>" target="_blank" class="button-secondary">
								<?php esc_html_e( 'Renew', 'wp-license-client' ); ?>
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
}
