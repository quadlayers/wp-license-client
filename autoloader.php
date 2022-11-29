<?php

namespace QUADLAYERS\LicenseClient;

/**
 * Autoloader class
 * This class handles management of the actual PHP autoloader.
 *
 * @since 1.0.0
 */
final class Autoloader {

	protected static $_instance;
	const PATH = __DIR__ . DIRECTORY_SEPARATOR;

	/**
	 * Register autoloader
	 */
	private function __construct() {
		spl_autoload_register( array( self::class, 'autoload' ) );
	}

	/**
	 * Loads a class file if one could be found.
	 *
	 * Note: This function is static so that the autoloader can be easily unregistered. If
	 * it was a class method we would have to unwrap the object to check the namespace.
	 *
	 * @param string $class_name The name of the class to autoload.
	 *
	 * @return bool Indicates whether or not a class file was loaded.
	 */
	public static function autoload( $class_name ) {

		if ( 0 !== strpos( $class_name, __NAMESPACE__ ) ) {
			return;
		}

		if ( ! class_exists( $class_name ) ) {

			$class_file = self::class_file( $class_name );

			if ( ! is_readable( $class_file ) ) {
				return false;
			}

			include_once $class_file;

			return true;
		}
	}

	/**
	 * Finds the file path for the given class.
	 *
	 * @param string $class_name The class to find.
	 *
	 * @return string|null $file_path The path to the file if found, null if no class was found.
	 */
	public static function class_file( $class_name ) {

		$class_name = str_replace( __NAMESPACE__, '', $class_name );

		$filename = strtolower( preg_replace( array( '/([a-z])([A-Z])/', '/_/', '/\\\/' ), array( '$1-$2', '-', DIRECTORY_SEPARATOR ), $class_name ) );

		if ( strpos( $filename, 'build' ) !== false ) {
			return self::PATH . $filename . '.php';
		}
		return self::PATH . 'lib/' . $filename . '.php';
	}

	/**
	 * Instantiate the singleton.
	 *
	 * @return self
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

Autoloader::instance();
