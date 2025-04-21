<?php

namespace QuadLayers\WP_License_Client\Backend\Cron;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;
use QuadLayers\WP_License_Client\Models\UserData as Model_User_Data;
use QuadLayers\WP_License_Client\Models\Activation as Model_Activation;
use QuadLayers\WP_License_Client\Api\Fetch\Activation\Create as API_Fetch_Activation_Create;

/**
 * Class VerifyLicense
 *
 * Handles automatic verification of license status
 *
 * @package QuadLayers\WP_License_Client\Backend\Cron
 */
class VerifyLicense {

	/**
	 * Plugin model
	 *
	 * @var Model_Plugin
	 */
	protected $plugin;

	/**
	 * User data model
	 *
	 * @var Model_User_Data
	 */
	protected $user_data;

	/**
	 * Activation model
	 *
	 * @var Model_Activation
	 */
	protected $activation;

	/**
	 * Base event name
	 */
	const CRON_EVENT_BASE = 'quadlayers_wp_license_verify';

	/**
	 * Plugin-specific cron event name
	 *
	 * @var string
	 */
	protected $cron_event_name;

	/**
	 * Constructor
	 *
	 * @param Model_Plugin     $plugin Plugin model
	 * @param Model_User_Data  $user_data User data model
	 * @param Model_Activation $activation Activation model
	 */
	public function __construct( Model_Plugin $plugin, Model_User_Data $user_data, Model_Activation $activation ) {
		$this->plugin     = $plugin;
		$this->user_data  = $user_data;
		$this->activation = $activation;

		$this->cron_event_name = self::CRON_EVENT_BASE . '_' . md5( $this->plugin->get_file() );

		add_filter( 'cron_schedules', array( $this, 'add_cron_schedules' ) );
		add_action( 'wp', array( $this, 'schedule_verification' ) );
		add_action( $this->cron_event_name, array( $this, 'verify_license' ) );

		register_deactivation_hook( $this->plugin->get_file(), array( $this, 'cleanup_schedule' ) );
	}

	/**
	 * Add monthly schedule to WordPress cron
	 *
	 * @param array $schedules
	 *
	 * @return array
	 */
	public function add_cron_schedules( $schedules ) {
		$schedules['monthly'] = array(
			'interval' => 30 * DAY_IN_SECONDS,
			'display'  => __( 'Once Monthly', 'wp-license-client' ),
		);

		return $schedules;
	}

	/**
	 * Schedule license verification if not already scheduled
	 */
	public function schedule_verification() {
		if ( ! wp_next_scheduled( $this->cron_event_name ) ) {
			wp_schedule_event( time(), 'monthly', $this->cron_event_name );
		}
	}

	/**
	 * Verify license status by re-activating with API
	 */
	public function verify_license() {

		$user_data = $this->user_data->get();

		if ( ! isset( $user_data['license_key'], $user_data['license_email'] ) ) {
			return;
		}

		try {
			$activation = ( new API_Fetch_Activation_Create( $this->plugin ) )->get_data(
				array_merge(
					(array) $user_data,
					array(
						'activation_site' => $this->plugin->get_activation_site(),
					)
				)
			);

			if ( isset( $activation->error, $activation->message ) ) {
				if ( in_array(
					$activation->error,
					array(
						2003,
						3000,
						3001,
						3002,
						3004,
					),
					true
				) ) {
					$this->activation->update( (array) $activation );
				}
				// If the error is not related to the license invalidation, we should not update the activation status.
				// This is to prevent the license from being invalidated if the API is down or the license key is invalid.
				return;
			}

			if ( is_wp_error( $activation ) ) {
				$this->activation->update(
					array(
						'error'   => $activation->get_error_code(),
						'message' => $activation->get_error_message(),
					)
				);
				return;
			}

			$this->activation->update( (array) $activation );

		} catch ( \Exception $e ) {

			$this->activation->update(
				array(
					'error'   => $e->getCode(),
					'message' => $e->getMessage(),
				)
			);

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log(
					sprintf(
						'Exception during license verification for %s: %s',
						$this->plugin->get_name(),
						$e->getMessage()
					)
				);
			}
		}
	}

	/**
	 * Clean up scheduled events when plugin is deactivated
	 */
	public function cleanup_schedule() {
		wp_clear_scheduled_hook( $this->cron_event_name );
	}
}
