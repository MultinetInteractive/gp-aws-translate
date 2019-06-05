<?php
defined( 'ABSPATH' ) or die( 'This plugin must be run within the scope of WordPress.' );

include_once '_constants.php';
global $supportedLanguages;

$availRegions = [
	"us-east-1"      => "US East (N. Virginia)",
	"us-east-2"      => "US East (Ohio)",
	"us-west-2"      => "US West (Oregon)",
	"ap-south-1"     => "Asia Pacific (Mumbai)",
	"ap-northeast-2" => "Asia Pacific (Seoul)",
	"ap-southeast-1" => "Asia Pacific (Singapore)",
	"ap-northeast-1" => "Asia Pacific (Tokyo)",
	"ca-central-1"   => "Canada (Central)",
	"eu-central-1"   => "EU (Frankfurt)",
	"eu-west-1"      => "EU (Ireland)"
];

?>
<div class="eduadmin wrap">
    <h2><?php echo esc_html( _x( 'GlotPress Amazon Translate Plugin', 'backend', 'gp-aws-translate' ) ); ?></h2>

    <form method="post" action="options.php">
		<?php settings_fields( 'gp-aws-translate' ); ?>
		<?php do_settings_sections( 'gp-aws-translate' ); ?>
        <div class="block">
            <p>
				<?php echo wp_kses_post( _x( 'Enter the <a href="http://docs.aws.amazon.com/console/iam/about-access-keys" target="_blank">access keys</a> for AWS Translate', 'backend', 'gp-aws-translate' ) ); ?>
            </p>
            <label for="aws-region"><?php _ex( 'AWS Region', 'backend', 'gp-aws-translate' ); ?></label><br/>
            <select name="aws-region" id="aws-region" required>
                <option value=""><?php _ex( 'Select data center', 'backend', 'gp-aws-translate' ); ?></option>
				<?php
				$selected_option = get_option( 'aws-region' );
				foreach ( $availRegions as $region => $name ) {
					?>
                    <option <?php echo $selected_option === $region ? ' selected="selected"' : ''; ?>
                            value="<?php echo esc_attr( $region ); ?>"><?php echo esc_html( $name ); ?></option>
					<?php
				}
				?>
            </select>
            <br/>
            <br/>
            <label for="aws-account-id"><?php _ex( 'AWS Account Id', 'backend', 'gp-aws-translate' ); ?></label><br/>
            <input type="password" required class="form-control api_hash" name="aws-account-id" id="aws-account-id"
                   value="<?php echo esc_attr( get_option( 'aws-account-id' ) ); ?>"
                   placeholder="<?php echo esc_attr_x( 'AWS Account Id', 'backend', 'gp-aws-translate' ); ?>"/>
            <br/>
            <br/>
            <label for="aws-secret-key"><?php _ex( 'AWS Secret Key', 'backend', 'gp-aws-translate' ); ?></label><br/>
            <input type="password" required class="form-control api_hash" name="aws-secret-key" id="aws-secret-key"
                   value="<?php echo esc_attr( get_option( 'aws-secret-key' ) ); ?>"
                   placeholder="<?php echo esc_attr_x( 'AWS Secret Key', 'backend', 'gp-aws-translate' ); ?>"/>
            <br/>
            <br/>
            <label for="gp-source-language"><?php _ex( 'Source language in GlotPress (That is supported in AWS Translate)', 'backend', 'gp-aws-translate' ); ?></label><br/>
            <select name="gp-source-language" id="gp-source-language" required>
                <option value=""><?php _ex( 'Select source language', 'backend', 'gp-aws-translate' ); ?></option>
				<?php
				$locales         = GP_Locales::locales();
				$selected_option = get_option( 'gp-source-language' );

				$added_languages = [];

				foreach ( $locales as $locale ) {
					if ( ! empty( $locale->lang_code_iso_639_1 ) && array_key_exists( $locale->lang_code_iso_639_1, $supportedLanguages ) && ! array_key_exists( $locale->lang_code_iso_639_1, $added_languages ) ) {
						?>
                        <option<?php echo $selected_option === $locale->lang_code_iso_639_1 ? ' selected="selected"' : ''; ?>
                                value="<?php echo esc_attr( $locale->lang_code_iso_639_1 ); ?>"><?php echo esc_html( $locale->english_name ); ?>
                            / <?php echo esc_html( $locale->native_name ); ?></option>
						<?php
						$added_languages[ $locale->lang_code_iso_639_1 ] = $locale;
					}
				}
				?>
            </select>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary"
                       value="<?php echo esc_attr_x( 'Save settings', 'backend', 'gp-aws-translate' ); ?>"/>
            </p>
        </div>
    </form>
</div>
