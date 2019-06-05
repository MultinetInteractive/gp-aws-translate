<?php
defined( 'ABSPATH' ) or die( 'This plugin must be run within the scope of WordPress.' );

include_once GP_AWS_PLUGIN_PATH . '/includes/_constants.php';

require GP_AWS_PLUGIN_PATH . '/vendor/autoload.php';

use Aws\Translate\TranslateClient;
use Aws\Exception\AwsException;

function gp_aws_frontend() {
	global $wp;
	global $supportedLanguages;

	preg_match( "/projects\/(.*?)\/([a-z]{2,2})\/.*\/?/i", $wp->query_vars["gp_route"], $matches );

	if ( count( $matches ) >= 0 ) {

		$currentLanguage = get_option( 'gp-source-language' );
		$locale          = GP_Locales::by_slug( $matches[2] );

		if ( ! array_key_exists( $locale->lang_code_iso_639_1, $supportedLanguages ) || $currentLanguage === $locale->lang_code_iso_639_1 ) {
			return;
		}

		$suggestTranslation = _x( 'Suggest translation', 'frontend', 'gp-aws-translate' );

		echo <<<FrontendCode
	<style type="text/css">
	.suggest_aws_translation {
		margin-left: 5px;
	}
	</style>
	<script type="text/javascript">
		"use strict";
		const targetLanguage = '$locale->lang_code_iso_639_1';
		
		function get_aws_translate_suggestion(target) {
		    let originalPhrase = jQuery(target).parents(".strings").find('.original')[0].innerText;
		    
		    jQuery.post('/wp-json/gp-aws-translate/v1/get-suggestion',
		    {
		        'source-string': originalPhrase,
		        'target-language': targetLanguage
		    },
		    function(suggestion) {
		        jQuery(target).parents(".textareas").find('.foreign-text').val(suggestion);
		    })
		}
		
		jQuery(function() {
		    let copyLink = jQuery('.editor .textareas .copy');
		    copyLink.each(function(i) {
		       let suggest_translation = jQuery('<a href="javascript://" class="suggest_aws_translation" onclick="get_aws_translate_suggestion(this);" tabindex="-1">$suggestTranslation</a>');
		       this.after(suggest_translation[0]);
		    });
		})
	</script>
FrontendCode;
	}
}

function aws_get_translation() {
	$incomingParams = $_POST;

	$text           = sanitize_text_field( $incomingParams['source-string'] );
	$targetLanguage = sanitize_text_field( $incomingParams['target-language'] );

	$awsRegion       = get_option( 'aws-region' );
	$awsKey          = get_option( 'aws-account-id' );
	$awsSecret       = get_option( 'aws-secret-key' );
	$currentLanguage = get_option( 'gp-source-language' );

	if ( empty( $awsRegion ) || empty( $awsKey ) || empty( $awsSecret ) || empty( $currentLanguage ) || empty( $targetLanguage ) || empty( $text ) ) {
		return [
			"error" => "Missing parameters"
		];
	}

	if ( $currentLanguage === $targetLanguage ) {
		return $text;
	}

	$client = new TranslateClient( [
		'region'      => $awsRegion,
		'version'     => '2017-07-01',
		'credentials' => [
			'key'    => $awsKey,
			'secret' => $awsSecret,
		]
	] );

	try {
		$res = $client->translateText( [
			'SourceLanguageCode' => $currentLanguage,
			'TargetLanguageCode' => $targetLanguage,
			'Text'               => $text
		] );

		return $res->get( "TranslatedText" );
	} catch ( AwsException $e ) {
		// output error message if fails
		return $e->getMessage();
	}
}
