<?php

namespace QuadLayers\WP_License_Client\Api\Rest\Endpoints\UserData;

use QuadLayers\WP_License_Client\Api\Rest\Endpoints\Base;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;
use QuadLayers\WP_License_Client\Models\UserData as Model_User_Data;
use QuadLayers\WP_License_Client\Models\Activation as Model_Activation;

/**
 * API_Rest_User_Data_Get Class
 *
 * @since 1.0.0
 */
class Get extends Base {

	/**
	 * Define rest route path
	 *
	 * @var string
	 */
	protected $rest_route = 'user-data';

	/**
	 * Process rest request. Ej: /wp-json/ql/WP_License_Client/xxx/user-data
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

		$user_data = $model_user_data->get();

		if ( false === $user_data ) {

			$response = array(
				'error'   => 1,
				'message' => esc_html__( 'User data could not be found.', 'wp-license-client' ),
			);

			return $this->handle_response( $response );
		}

		return $this->handle_response( $user_data );
	}

	/**
	 * Get rest method
	 *
	 * @return string POST
	 */
	public function get_rest_method() {
		return \WP_REST_Server::READABLE;
	}
}
