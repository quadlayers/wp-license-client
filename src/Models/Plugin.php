<?php

namespace QuadLayers\LicenseClient\Models;

/**
 * Model_Plugin Class
 * This class handles plugin data
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Plugin __DIR__
	 *
	 * @var string
	 */
	private $dir;

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
	 * Plugin author URL
	 *
	 * @var string
	 */
	private $plugin_url;

	/**
	 * Plugin file path
	 *
	 * @var string
	 */
	private $plugin_file;

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
	 * Setup class
	 *
	 * @param array $client_data Plugin data.
	 */
	public function __construct( array $client_data ) {

		foreach ( $client_data as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
				$this->{$key} = $value;
			}
		}

		$this->try_get_plugin_file();
	}

	public function get_plugin_slug() {
		if ( ! $this->get_plugin_file() ) {
			return false;
		}
		$plugin_slug = basename( $this->get_plugin_file(), '.php' );
		return $plugin_slug;
	}

	public function is_valid() {
		if ( ! $this->get_plugin_file() ) {
			return false;
		}
		if ( ! is_file( $this->get_plugin_file() ) ) {
			return false;
		}
		return true;
	}

	public function get_plugin_base() {
		if ( ! $this->get_plugin_file() ) {
			return false;
		}
		$plugin_base = plugin_basename( $this->get_plugin_file() );
		return $plugin_base;
	}

	public function get_plugin_version() {
		$plugin_data = $this->get_wp_plugin_data( $this->get_plugin_file() );
		if ( empty( $plugin_data['Version'] ) ) {
			return false;
		}
		return $plugin_data['Version'];
	}

	public function get_plugin_name() {
		$plugin_data = $this->get_wp_plugin_data( $this->get_plugin_file() );
		if ( empty( $plugin_data['Name'] ) ) {
			return false;
		}
		return $plugin_data['Name'];
	}

	public function get_plugin_url() {

		if ( $this->plugin_url ) {
			return $this->plugin_url;
		}

		$plugin_data = $this->get_wp_plugin_data( $this->get_plugin_file() );

		if ( empty( $plugin_data['PluginURI'] ) ) {
			return false;
		}

		return $plugin_data['PluginURI'];
	}

	private function get_wp_plugin_data() {
		if ( ! $this->get_plugin_file() ) {
			return false;
		}
		return get_plugin_data( $this->get_plugin_file() );
	}

	public function get_api_url() {
		return $this->api_url;
	}

	public function get_product_key() {
		return $this->product_key;
	}

	public function get_plugin_file() {
		return $this->plugin_file;
	}

	private function try_get_plugin_file() {

		if ( $this->plugin_file && is_file( $this->plugin_file ) ) {
			return $this->plugin_file;
		}

		$file_basename = plugin_basename( $this->dir );

		$file_folders = explode( '/', $file_basename );

		if ( ! isset( $file_folders[0] ) ) {
			return false;
		}

		$plugin_folder = $file_folders[0];

		$plugin_file = wp_normalize_path( WP_PLUGIN_DIR . '/' . $plugin_folder . '/' . $plugin_folder . '.php' );

		if ( ! is_file( $plugin_file ) ) {
			return false;
		}

		$this->plugin_file = $plugin_file;

		return $plugin_file;
	}

	/**
	 * Get the current site url to send to the API server
	 *
	 * @return url
	 */
	public function get_activation_site() {
		return home_url();
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
			return $this->get_plugin_slug();
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
		if ( ! $this->license_menu_slug || ! is_string( $this->license_menu_slug ) ) {
			return $this->get_parent_menu_slug() . '_license';
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

}
