<?php

namespace QUADLAYERS\LicenseClient\Api\Fetch\Product;

use QUADLAYERS\LicenseClient\Api\Fetch\Base;

/**
 * API_Fetch_Product_Information Class
 */

class Information extends Base {

	public function get_rest_path() {
		return 'product/information';
	}

	public static function get_rest_method() {
		return 'GET';
	}

}
