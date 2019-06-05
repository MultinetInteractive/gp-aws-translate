<?php
defined( 'ABSPATH' ) or die( 'This plugin must be run within the scope of WordPress.' );

class GP_AWS_API_Controller {
	public $namespace;

	public function __construct() {
		$this->namespace = 'gp-aws-translate/v1';
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/get-suggestion', array(
			'methods'  => 'POST',
			'callback' => 'aws_get_translation',
			'args'     => array(
				'source-string'    => array( 'required' => true ),
				'target-language' => array( 'required' => true ),
			),
		) );
	}
}
