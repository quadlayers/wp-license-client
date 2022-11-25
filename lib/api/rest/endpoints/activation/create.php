<?php
namespace QUADLAYERS\LicenseClient\Api\Rest\Endpoints\Activation;

use QUADLAYERS\LicenseClient\Api\Rest\Endpoints\Base as Base;
use QUADLAYERS\LicenseClient\Api\Fetch\Activation\Create as API_Fetch_Activation_Create;

use QUADLAYERS\LicenseClient\Models\Plugin as Model_Plugin;
use QUADLAYERS\LicenseClient\Models\UserData as Model_User_Data;
use QUADLAYERS\LicenseClient\Models\Activation as Model_Activation;

/**
 * API_Rest_Create_Activation_License Class
 */

class Create extends Base {

	protected $rest_route = 'activation';

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

		$fetch = new API_Fetch_Activation_Create( $model_plugin );

		$data = $fetch->get_data(
			array_merge(
				(array) $body,
				array(
					'activation_site' => $model_plugin->get_activation_site(),
					'product_key'     => $model_plugin->get_product_key(),
				)
			)
		);

		if ( isset( $data->error ) ) {
			$response = array(
				'error'   => isset( $data->error ) ? $data->error : null,
				'message' => isset( $data->message ) ? $data->message : null,
			);
			return $this->handle_response( $data );
		}

		$model_activation->create( (array) $data );

		return $this->handle_response( $data );
	}

	public function get_rest_method() {
		return \WP_REST_Server::CREATABLE;
	}

}
