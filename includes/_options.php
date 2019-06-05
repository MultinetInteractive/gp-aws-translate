<?php
defined( 'ABSPATH' ) or die( 'This plugin must be run within the scope of WordPress.' );

add_action( 'admin_init', 'gp_aws_init' );
add_action( 'admin_menu', 'gp_aws_backend_menu' );

function gp_aws_init() {
	register_setting( 'gp-aws-translate', 'aws-region' );
	register_setting( 'gp-aws-translate', 'aws-account-id' );
	register_setting( 'gp-aws-translate', 'aws-secret-key' );
	register_setting( 'gp-aws-translate', 'gp-source-language' );
}

function gp_aws_backend_menu() {
	add_menu_page(
		_x( 'GlotPress Amazon Translate Plugin', 'backend', 'gp-aws-translate' ),
		_x( 'GP AWS Translate', 'backend', 'gps-aws-translate' ),
		'administrator',
		'glotpress-aws-translate',
		'gp_aws_setting_page'
	);
}

function gp_aws_setting_page() {
	include_once 'settings_page.php';
}
