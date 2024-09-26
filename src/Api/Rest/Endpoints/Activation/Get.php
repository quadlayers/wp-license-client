<?php

namespace QuadLayers\WP_License_Client\Api\Rest\Endpoints\Activation;

use QuadLayers\WP_License_Client\Api\Rest\Endpoints\Base;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;
use QuadLayers\WP_License_Client\Models\UserData as Model_User_Data;
use QuadLayers\WP_License_Client\Models\Activation as Model_Activation;


/**
 * API_Rest_Activation_License_Get Class
 *
 * @since 1.0.0
 */
class Get extends Base {

	/**
	 * Define rest route path
	 *
	 * @var string
	 */
	protected $rest_route = 'activation';

	/**
	 * Process rest request. Ej: /wp-json/ql/WP_License_Client/xxx/activation
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request Request data.
	 * @param Model_Plugin     $model_plugin Model_Plugin instance.
	 * @param Model_Activation $model_activation Model_Activation instance.
	 * @param Model_User_Data  $model_user_data Model_User_Data instance.
	 * @return array
	 */
	public function callback( \WP_REST_Request $request, Model_Plugin $model_plugin, Model_Activation $model_activation, Model_User_Data $model_user_data ) {

		$activation = $model_activation->get();

		if ( empty( $activation ) ) {
			$response = array(
				'error'   => true,
				'message' => esc_html__( 'Unknown error. Please delete and try again.', 'wp-license-client' ),
			);
			return $this->handle_response( $response );
		}

		return $this->handle_response( $activation );
	}

	/**
	 * Get rest method
	 *
	 * @return string GET
	 */
	public function get_rest_method() {
		return \WP_REST_Server::READABLE;
	}
}
