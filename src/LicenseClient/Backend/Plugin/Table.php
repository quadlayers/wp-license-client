<?php

namespace LicenseClient\Backend\Plugin;

use LicenseClient\Models\Plugin as Model_Plugin;

/**
 * Controller_Plugin_Table Class
 *
 * Implement plugin version notification in plugins table.
 *
 * @since 1.0.0
 */
class Table {

	/**
	 * Instantiated Model_Plugin in the constructor.
	 *
	 * @var Model_Plugin
	 */
	private $plugin;

	/**
	 * Setup class.
	 *
	 * @param Model_Plugin $plugin
	 */
	public function __construct( Model_Plugin $plugin ) {
		$this->plugin = $plugin;
		add_action(
			'admin_init',
			function() {
				add_filter( 'plugins_api', array( $this, 'add_fetch_data' ), 10, 3 );
				add_action( 'in_plugin_update_message-' . $this->plugin->get_plugin_base(), array( $this, 'add_license_notification' ), 10, 2 );
			}
		);

	}

	/**
	 * Include fetched data in transient to the plugin in the plugins table.
	 *
	 * @param object $return
	 * @param string $action
	 * @param object $args
	 * @return object
	 */
	function add_fetch_data( $return, $action, $args ) {

		if ( 'plugin_information' !== $action ) {
			return $return;
		}

		if ( $args->slug != $this->plugin->get_plugin_slug() ) {
			return $return;
		}

		if ( $plugin = get_site_transient( 'update_plugins' )->no_update[ $this->plugin->get_plugin_base() ] ) {

			if ( isset( $plugin->sections['screenshots'] ) && is_array( $plugin->sections['screenshots'] ) ) {
				$plugin->sections['screenshots'] = $this->add_screenshots( $plugin->sections['screenshots'] );
			}

			return $plugin;
		}

		return $return;
	}

	/**
	 * Add product screenshots to the plugin in the View details modal of the plugins table.
	 *
	 * @param array $screenshots
	 * @return string
	 */
	function add_screenshots( array $screenshots = array() ) {
		ob_start();
		?>
			<ol>
				<?php foreach ( $screenshots as $key => $image ) : ?>
					<li><a href="<?php echo esc_url( $image->src ); ?>"><img src="<?php echo esc_url( $image->src ); ?>" alt="<?php echo esc_html( $image->caption ); ?>"></a></li>
				<?php endforeach; ?>
			</ol>
		<?php
		return ob_get_clean();
	}

	/**
	 * Require license validation notification in the plugins table if the automatic updates are disabled.
	 *
	 * @param object $plugin_data
	 * @param object $response
	 * @return string
	 */
	function add_license_notification( $plugin_data, $response ) {

		if ( empty( $response->package ) ) {
			printf(
				'</p></div><span class="notice notice-error notice-alt" style="display:block; padding: 10px;"><b>%s</b> %s</span>',
				__ql_translate( 'Activate your license.' ),
				sprintf(
					__ql_translate( 'Please visit %1$s to activate the license or %2$s in our website.' ),
					sprintf(
						'<a href="%s" target="_blank">%s</a>',
						esc_url( $this->plugin->get_menu_license_url() ),
						__ql_translate( 'settings' ),
					),
					sprintf(
						'<a href="%s" target="_blank">%s</a>',
						esc_url( $this->plugin->get_plugin_url() ),
						__ql_translate( 'purchase' )
					)
				)
			);
		}
	}

}
