<?php
defined( 'ABSPATH' ) or die( 'This plugin must be run within the scope of WordPress.' );
define( 'GP_AWS_PLUGIN_PATH', dirname( __FILE__ ) );
/*
 * Plugin Name: GlotPress Amazon Translate Plugin
 * Plugin URI: https://github.com/MultinetInteractive/gp-aws-translate
 * Description: Enables suggestions for translations
 * Version: 1.1.1
 * Author: Chris Gårdenberg, MultiNet Interactive AB
 * Author URI: https://github.com/itssimple
 * License: MIT
 * Text Domain:	gp-aws-translate
 * Domain Path: /languages/
*/

global $gpAwsRestController;

include_once 'includes/_options.php';
include_once 'gp-extensions/gp_aws_api_controller.php';
include_once 'gp-extensions/gp_aws_frontend.php';

$gpAwsRestController = new GP_AWS_API_Controller();

add_action( 'rest_api_init', function () {
	global $gpAwsRestController;
	$gpAwsRestController->register_routes();
} );

add_action( 'gp_footer', 'gp_aws_frontend', 10, 1 );
