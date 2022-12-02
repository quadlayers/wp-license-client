<?php
namespace QuadLayers\LicenseClient\Api\Rest\Endpoints\Activation;

use QuadLayers\LicenseClient\Api\Rest\Endpoints\Base as Base;
use QuadLayers\LicenseClient\Api\Fetch\Activation\Create as API_Fetch_Activation_Create;

use QuadLayers\LicenseClient\Models\Plugin as Model_Plugin;
use QuadLayers\LicenseClient\Models\UserData as Model_User_Data;
use QuadLayers\LicenseClient\Models\Activation as Model_Activation;

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
	 * Process rest request. Ej: /wp-json/ql/licenseClient/xxx/activation
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

		$activation = $fetch->get_data(
			array_merge(
				(array) $body,
				array(
					'activation_site' => $model_plugin->get_activation_site(),
					'product_key'     => $model_plugin->get_product_key(),
				)
			)
		);

		if ( isset( $activation->error ) ) {
			$response = array(
				'error'   => isset( $activation->error ) ? $activation->error : null,
				'message' => isset( $activation->message ) ? $activation->message : null,
			);
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
