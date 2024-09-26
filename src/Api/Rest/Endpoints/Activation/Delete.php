<?php
namespace QuadLayers\WP_License_Client\Api\Rest\Endpoints\Activation;

use QuadLayers\WP_License_Client\Api\Rest\Endpoints\Base;
use QuadLayers\WP_License_Client\Api\Fetch\Activation\Delete as API_Fetch_Activation_Delete;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;
use QuadLayers\WP_License_Client\Models\UserData as Model_User_Data;
use QuadLayers\WP_License_Client\Models\Activation as Model_Activation;

/**
 * API_Rest_Activation_License_Delete Class
 *
 * @since 1.0.0
 */
class Delete extends Base {

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

		$delete = ( new API_Fetch_Activation_Delete( $model_plugin ) )->get_data(
			array(
				'license_key'         => isset( $activation['license_key'] ) ? $activation['license_key'] : null,
				'activation_instance' => isset( $activation['activation_instance'] ) ? $activation['activation_instance'] : null,
			)
		);

		if ( isset( $delete->error ) ) {
			$response = array(
				'error'   => isset( $delete->error ) ? $delete->error : null,
				'message' => isset( $delete->message ) ? $delete->message : null,
			);
			return $this->handle_response( $response );
		}

		$model_activation->delete();

		return $this->handle_response( $delete );
	}

	/**
	 * Get rest method
	 *
	 * @return string DELETE
	 */
	public function get_rest_method() {
		return \WP_REST_Server::DELETABLE;
	}
}
