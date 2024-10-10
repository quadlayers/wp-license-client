<?php

namespace QuadLayers\WP_License_Client\Models;

use QuadLayers\WP_License_Client\Traits\PluginDataByFile;

/**
 * Model_Plugin Class
 * This class handles plugin data
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Server API url
	 *
	 * @var string
	 */
	private $api_url;

	/**
	 * Plugin menu slug
	 *
	 * @var string
	 */
	private $parent_menu_slug = null;

	/**
	 * Custom license menu slug
	 *
	 * @var string
	 */
	private $license_menu_slug = null;

	/**
	 * Product key from API server
	 *
	 * @var string
	 */
	private $product_key;

	/**
	 * Custom license url
	 *
	 * @var string
	 */
	private $license_url;

	/**
	 * Custom license key url
	 *
	 * @var string
	 */
	private $license_key_url;

	/**
	 * Custom support url
	 *
	 * @var string
	 */
	private $support_url;

	/**
	 * Activation delete url
	 *
	 * @var string
	 */
	private $activation_delete_url;

	/**
	 * Setup class
	 *
	 * @param array $client_data Plugin data.
	 */

	use PluginDataByFile;

	public function __construct( array $client_data ) {

		foreach ( $client_data as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
				$this->{$key} = $value;
			}
		}
	}

	public function get_api_url() {
		return $this->api_url;
	}

	public function get_product_key() {
		return $this->product_key;
	}

	/**
	 * Get the current site url to send to the API server
	 *
	 * @return url
	 */
	public function get_activation_site() {
		return home_url();
	}

	public function get_activation_delete_url() {
		if ( $this->activation_delete_url && is_string( $this->activation_delete_url ) ) {
			return $this->activation_delete_url;
		}
	}

	/**
	 * Get parent menu slug if set or create parent menu slug based on plugin slug.
	 *
	 * @return string
	 */
	public function get_parent_menu_slug() {

		/**
		 * Disable menu if parent menu slug is set to false
		 */
		if ( false === $this->parent_menu_slug ) {
			return false;
		}

		/**
		 * Enable menu if parent menu slug is not set
		 */
		if ( null === $this->parent_menu_slug || ! is_string( $this->parent_menu_slug ) ) {
			return $this->get_slug();
		}

		/**
		 * Append menu to parent menu slug if set
		 */
		return $this->parent_menu_slug;
	}

	/**
	 * Check if the developer set parent_menu_slug to false to disable the menu.
	 * Construct a menu slug based on parent_menu_slug or return custom license_menu slug.
	 *
	 * @return string
	 */
	public function get_license_menu_slug() {
		if ( ! $this->get_parent_menu_slug() ) {
			return false;
		}
		if ( ! $this->license_menu_slug ) {
			return false;
		}

		return $this->license_menu_slug;
	}

	public function get_menu_license_url() {
		if ( $this->license_url && is_string( $this->license_url ) ) {
			return $this->license_url;
		}
		if ( ! $this->get_license_menu_slug() ) {
			return false;
		}
		return admin_url( 'admin.php?page=' . $this->get_license_menu_slug() );
	}

	public function get_license_key_url() {
		if ( $this->license_key_url && is_string( $this->license_key_url ) ) {
			return $this->license_key_url;
		}
	}

	public function get_support_url() {
		if ( $this->support_url && is_string( $this->support_url ) ) {
			return $this->support_url;
		}
	}
}
