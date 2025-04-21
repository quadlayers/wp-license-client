<?php

namespace QuadLayers\WP_License_Client\Backend\Notice;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;

/**
 * Class StatusChange
 *
 * Handles the display of license status change notifications
 *
 * @package QuadLayers\WP_License_Client\Backend\Notice
 */
class StatusChange {

	/**
	 * Plugin model instance
	 *
	 * @var Model_Plugin
	 */
	protected $plugin;

	/**
	 * Constructor
	 *
	 * @param Model_Plugin $plugin Plugin model instance
	 */
	public function __construct( Model_Plugin $plugin ) {
		$this->plugin = $plugin;

		// Add admin notices hook
		add_action( 'admin_notices', array( $this, 'display_status_change_notice' ) );
	}

	/**
	 * Display license status change notices
	 */
	public function display_status_change_notice() {
		// Check if we have a status change notice
		$transient_key = 'quadlayers_license_status_change_' . md5( $this->plugin->get_file() );
		$notice        = get_transient( $transient_key );

		if ( ! isset( $notice['message'], $notice['type'], $notice['dismissible'] ) ) {
			return;
		}

		// Output the notice
		$class   = 'notice notice-' . $notice['type'] . ( $notice['dismissible'] ? ' is-dismissible' : '' );
		$message = wp_kses(
			$notice['message'],
			array(
				'a'      => array(
					'href'   => array(),
					'target' => array(),
					'rel'    => array(),
				),
				'strong' => array(),
				'em'     => array(),
			)
		);

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );

		// Add script to handle dismissible notice and remove the transient
		if ( $notice['dismissible'] ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$(document).on('click', '.notice-<?php echo esc_js( $notice['type'] ); ?>.is-dismissible .notice-dismiss', function() {
						$.ajax({
							url: ajaxurl,
							data: {
								action: 'quadlayers_dismiss_license_notice',
								transient: '<?php echo esc_js( $transient_key ); ?>',
								security: '<?php echo esc_js( wp_create_nonce( 'quadlayers_dismiss_license_notice' ) ); ?>'
							}
						});
					});
				});
			</script>
			<?php

			// Add Ajax handler for dismissing the notice
			add_action( 'wp_ajax_quadlayers_dismiss_license_notice', array( $this, 'dismiss_notice' ) );
		}
	}

	/**
	 * Ajax handler to dismiss the notice
	 */
	public function dismiss_notice() {
		// Verify nonce
		check_ajax_referer( 'quadlayers_dismiss_license_notice', 'security' );

		// Check if user has permission
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have permission to perform this action', 'wp-license-client' ) );
		}

		// Get the transient key and delete it
		$transient_key = isset( $_POST['transient'] ) ? sanitize_text_field( $_POST['transient'] ) : '';
		if ( ! empty( $transient_key ) ) {
			delete_transient( $transient_key );
		}

		wp_die();
	}
}
