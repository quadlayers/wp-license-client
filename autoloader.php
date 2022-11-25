<?php

namespace QUADLAYERS\LicenseClient;

final class Autoloader {

	protected static $_instance;
	const PATH = __DIR__ . DIRECTORY_SEPARATOR;

	private function __construct() {
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	public function autoload( $class_to_load ) {

		if ( 0 !== strpos( $class_to_load, __NAMESPACE__ ) ) {
			return;
		}

		if ( ! class_exists( $class_to_load ) ) {

			$class_file = $this->class_file( $class_to_load );

			if ( is_readable( $class_file ) ) {
				include_once $class_file;
			} else {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					$warning_message = sprintf( __ql_translate( '"Can\'t find %1$s" for "%2$s" in "%3$s".' ), $class_file, $class_to_load, __NAMESPACE__ );
					error_log( $warning_message, E_USER_NOTICE );
				}
			}
		}
	}

	public static function class_file( $class_name ) {

		$class_name = str_replace( __NAMESPACE__, '', $class_name );

		$filename = strtolower( preg_replace( array( '/([a-z])([A-Z])/', '/_/', '/\\\/' ), array( '$1-$2', '-', DIRECTORY_SEPARATOR ), $class_name ) );

		if ( strpos( $filename, 'build' ) !== false ) {
			return self::PATH . $filename . '.php';
		}
		return self::PATH . 'lib/' . $filename . '.php';
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

Autoloader::instance();
