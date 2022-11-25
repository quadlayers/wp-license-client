<?php

namespace QUADLAYERS\LicenseClient\Api\Fetch\Activation;

use QUADLAYERS\LicenseClient\Api\Fetch\Base;

/**
 * API_Fetch_Activation_Create Class
 */

class Create extends Base {

	public function get_rest_path() {
		return 'activation';
	}

	public static function get_rest_method() {
		return 'POST';
	}

}
