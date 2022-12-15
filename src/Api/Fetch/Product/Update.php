<?php

namespace QuadLayers\LicenseClient\Api\Fetch\Product;

use QuadLayers\LicenseClient\Api\Fetch\Base;

/**
 * API_Fetch_Product_Update Class
 *
 * @since 1.0.0
 */
class Update extends Base {

	/**
	 * Get rest route path
	 *
	 * @return string
	 */
	public function get_rest_path() {
		return 'product/update';
	}

	/**
	 * Get rest method
	 *
	 * @return string GET
	 */
	public static function get_rest_method() {
		return 'GET';
	}

}
