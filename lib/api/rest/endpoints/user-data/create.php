<?php
namespace QUADLAYERS\LicenseClient\Api\Rest\Endpoints\UserData;

use QUADLAYERS\LicenseClient\Api\Rest\Endpoints\Base as Base;

use QUADLAYERS\LicenseClient\Models\Plugin as Model_Plugin;
use QUADLAYERS\LicenseClient\Models\UserData as Model_User_Data;
use QUADLAYERS\LicenseClient\Models\Activation as Model_Activation;
/**
 * API_Rest_Create_User_Data Class
 */

class Create extends Base {

	protected $rest_route = 'user-data';

		public function callback( \WP_REST_Request $request, Model_Plugin $model_plugin, Model_Activation $model_activation, Model_User_Data $model_user_data ) {

		$body = json_decode( $request->get_body() );

		if ( empty( $body->license_email ) ) {
			$response = array(
				'error'   => 1,
				'message' => __ql_translate( 'license_email not setted.' ),
			);
			return $this->handle_response( $response );
		}

		if ( empty( $body->license_key ) ) {
			$response = array(
				'error'   => 1,
				'message' => __ql_translate( 'license_key not setted.' ),
			);
			return $this->handle_response( $response );
		}

		$user_data = $model_user_data->create( (array) $body );

		if ( ! $user_data ) {
			$response = array(
				'error'   => 1,
				'message' => __ql_translate( 'User data could not be saved.' ),
			);
			return $this->handle_response( $response );
		}

		return $this->handle_response( $user_data );
	}

	public function get_rest_method() {
		return \WP_REST_Server::CREATABLE;
	}
}
