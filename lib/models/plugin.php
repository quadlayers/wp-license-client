<?php

namespace QUADLAYERS\LicenseClient\Models;

/**
 * Model_Plugin Class
 */
class Plugin {

	private $dir;

	private $api_url;

	private $parent_menu_slug = null;

	private $plugin_url;

	private $plugin_file;

	private $product_key;

	private $license_url;

	public function __construct( array $plugin_data ) {

		foreach ( $plugin_data as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
				$this->{$key} = $value;
			}
		}

		/* $this->set_plugin_file(); */
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
	
/* 	private function set_plugin_file() {

		if ( $this->plugin_file && is_file( $this->plugin_file ) ) {
			return $this->plugin_file;
		}

		$backfiles = debug_backtrace();

		if ( ! isset( $backfiles[0]['file'] ) ) {
			return false;
		}

		$file_basename = plugin_basename( $backfiles[4]['file'] );

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
	} */

	public function get_activation_site() {
		return home_url();
	}

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

	public function get_license_menu_slug() {
		if ( ! $this->get_parent_menu_slug() ) {
			return false;
		}
		return $this->get_parent_menu_slug() . '_license';
	}

	public function get_menu_license_url() {
		if ( $this->license_url && is_string( $this->license_url ) ) {
			return $this->license_url;
		}
		if ( ! $this->get_license_menu_slug() ) {
			return false;
		}
		return menu_page_url( $this->get_license_menu_slug(), false );
	}

}
