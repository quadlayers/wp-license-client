<?php
namespace QuadLayers\WP_License_Client\Api\Rest\Endpoints\Activation;

use QuadLayers\WP_License_Client\Api\Rest\Endpoints\Base;
use QuadLayers\WP_License_Client\Api\Fetch\Activation\Create as API_Fetch_Activation_Create;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;
use QuadLayers\WP_License_Client\Models\UserData as Model_User_Data;
use QuadLayers\WP_License_Client\Models\Activation as Model_Activation;

/**
 * API_Rest_Activation_License_Create Class
 *
 * @since 1.0.0
 */
class Create extends Base {

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

		$body = json_decode( $request->get_body() );

		if ( empty( $body->license_email ) ) {
			$response = array(
				'error'   => 1,
				'message' => esc_html__( 'license_email not setted.', 'wp-license-client' ),
			);
			return $this->handle_response( $response );
		}
		if ( empty( $body->license_key ) ) {
			$response = array(
				'error'   => 1,
				'message' => esc_html__( 'license_key not setted.', 'wp-license-client' ),
			);
			return $this->handle_response( $response );
		}

		$activation = ( new API_Fetch_Activation_Create( $model_plugin ) )->get_data(
			array_merge(
				(array) $body,
				array(
					'activation_site' => $model_plugin->get_activation_site(),
				)
			)
		);

		if ( isset( $activation->error ) ) {
			$response = array(
				'error'   => isset( $activation->error ) ? $activation->error : null,
				'message' => isset( $activation->message ) ? $activation->message : null,
			);

			$model_activation->delete();
			return $this->handle_response( $activation );
		}

		$model_activation->create( (array) $activation );

		return $this->handle_response( $activation );
	}

	/**
	 * Get rest method
	 *
	 * @return string POST
	 */
	public function get_rest_method() {
		return \WP_REST_Server::CREATABLE;
	}
}
