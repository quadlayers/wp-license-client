<?php
namespace QUADLAYERS\LicenseClient\Api\Rest\Endpoints\Activation;

use QUADLAYERS\LicenseClient\Api\Rest\Endpoints\Base as Base;
use QUADLAYERS\LicenseClient\Api\Fetch\Activation\Delete as API_Fetch_Activation_Delete;

use QUADLAYERS\LicenseClient\Models\Plugin as Model_Plugin;
use QUADLAYERS\LicenseClient\Models\UserData as Model_User_Data;
use QUADLAYERS\LicenseClient\Models\Activation as Model_Activation;

/**
 * API_Rest_Delete_Activation_License Class
 */

class Delete extends Base {

	protected $rest_route = 'activation';

		public function callback( \WP_REST_Request $request, Model_Plugin $model_plugin, Model_Activation $model_activation, Model_User_Data $model_user_data ) {

		$fetch = new API_Fetch_Activation_Delete( $model_plugin );

		$activation = $model_activation->get();

		$data = $fetch->get_data(
			array(
				'license_key'         => isset( $activation['license_key'] ) ? $activation['license_key'] : null,
				'activation_instance' => isset( $activation['activation_instance'] ) ? $activation['activation_instance'] : null,
			)
		);

		$model_activation->delete();

		if ( isset( $data->error ) ) {
			$response = array(
				'error'   => isset( $data->error ) ? $data->error : null,
				'message' => isset( $data->message ) ? $data->message : null,
			);
			return $this->handle_response( $response );
		}

		return $this->handle_response( $data );
	}

	public function get_rest_method() {
		return \WP_REST_Server::DELETABLE;
	}
}
