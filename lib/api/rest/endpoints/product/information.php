<?php
namespace QUADLAYERS\LicenseClient\Api\Rest\Endpoints\Product;

use QUADLAYERS\LicenseClient\Api\Rest\Endpoints\Base as Base;
use QUADLAYERS\LicenseClient\Api\Fetch\Product\Information as API_Fetch_Product_Information;


use QUADLAYERS\LicenseClient\Models\Plugin as Model_Plugin;
use QUADLAYERS\LicenseClient\Models\UserData as Model_User_Data;
use QUADLAYERS\LicenseClient\Models\Activation as Model_Activation;

/**
 * API_Rest_Request_Product_Information Class
 */

class Information extends Base {

	protected $rest_route = 'product/version';

		public function callback( \WP_REST_Request $request, Model_Plugin $model_plugin, Model_Activation $model_activation, Model_User_Data $model_user_data ) {

		$body = json_decode( $request->get_body() );

		if ( empty( $body->activation_instance ) ) {
			$response = array(
				'error'   => 1,
				'message' => __ql_translate( 'activation_instance not setted.' ),
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

		$activation_instance = trim( $body->activation_instance );
		$license_key         = trim( $body->license_key );

		$fetch = new API_Fetch_Product_Information( $model_plugin );

		$data = $fetch->get_data(
			array(
				'license_key'         => $license_key,
				'activation_instance' => $activation_instance,
			)
		);

		if ( isset( $data->error ) ) {
			$response = array(
				'error'   => isset( $data->error ) ? $data->error : null,
				'message' => isset( $data->message ) ? $data->message : null,
			);
			return $this->handle_response( $data );
		}

		return $this->handle_response( $data );
	}

	public function get_rest_method() {
		return \WP_REST_Server::CREATABLE;
	}
}
