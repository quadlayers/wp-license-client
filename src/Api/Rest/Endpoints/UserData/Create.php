<?php
namespace QuadLayers\LicenseClient\Api\Rest\Endpoints\UserData;

use QuadLayers\LicenseClient\Api\Rest\Endpoints\Base as Base;

use QuadLayers\LicenseClient\Models\Plugin as Model_Plugin;
use QuadLayers\LicenseClient\Models\UserData as Model_User_Data;
use QuadLayers\LicenseClient\Models\Activation as Model_Activation;

/**
 * API_Rest_User_Data_Create Class
 *
 * @since 1.0.0
 */
class Create extends Base {

	/**
	 * Define rest route path
	 *
	 * @var string
	 */
	protected $rest_route = 'user-data';

	/**
	 * Process rest request. Ej: /wp-json/ql/licenseClient/xxx/user-data
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

	/**
	 * Get rest method
	 *
	 * @return string POST
	 */
	public function get_rest_method() {
		return \WP_REST_Server::CREATABLE;
	}
}
