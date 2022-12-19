<?php
namespace QuadLayers\WP_License_Client\Api\Rest\Endpoints;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;
use QuadLayers\WP_License_Client\Models\UserData as Model_User_Data;
use QuadLayers\WP_License_Client\Models\Activation as Model_Activation;
/**
 * Route Interface
 */

interface RouteInterface {

	public function callback( \WP_REST_Request $request, Model_Plugin $model_plugin, Model_Activation $model_activation, Model_User_Data $model_user_data );

	public function get_name();

	public function get_rest_route();

	public function get_rest_path();

	public function get_rest_method();

	public function get_rest_args();

	public function get_rest_permission();

	public function get_rest_url();
}
