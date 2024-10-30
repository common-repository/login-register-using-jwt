<?php
/**
 * Core
 *
 * Login with JWT Method view Handler.
 *
 * @category   Common, Core
 * @package    MoJWT\MethodViewHandler
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT\MethodViewHandler;

/**
 * Class to JWT Login Method View Handler.
 *
 * @category Common, Core
 * @package  MoJWT\MethodViewHandler\LoginJWTView
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class LoginJWTView {
	/**
	 * Method name
	 *
	 * @var String $method_name
	 * */
	private $method_name;

	/**
	 * Allow users to login
	 *
	 * @var String $method_name
	 * */
	private $jwt_allow_login;

	/**
	 * Image of method
	 *
	 * @var String $image_name
	 * */
	private $image_name;

	/**
	 * Method slug
	 *
	 * @var String $method_slug
	 * */
	private $method_slug;

	/**
	 * Method priority to view
	 *
	 * @var String $priority
	 * */
	private $priority;

	/**
	 * Inbuilt secret
	 *
	 * @var String $inbuilt_secret
	 * */
	private $inbuilt_secret;

	/**
	 * Redirection by jwt
	 *
	 * @var String $jwt_redirection
	 * */
	private $jwt_redirection;

	/**
	 * Url from jwt
	 *
	 * @var String $get_jwt_from_url
	 * */
	private $get_jwt_from_url;

	/**
	 * Cookie from jwt
	 *
	 * @var String $get_jwt_from_cookie
	 * */
	private $get_jwt_from_cookie;

	/**
	 * Method for token validation
	 *
	 * @var String $token_validation_method
	 * */
	private $token_validation_method;

	/**
	 * Introspection endpoint
	 *
	 * @var String $introspection_endpoint
	 * */
	private $introspection_endpoint;

	/**
	 * Client ID
	 *
	 * @var String $client_id
	 * */
	private $client_id;

	/**
	 * Client Secret
	 *
	 * @var String $client_secret
	 * */
	private $client_secret;

	/**
	 * Jwks endpoints
	 *
	 * @var String $jwks_endpoint
	 * */
	private $jwks_endpoint;

	/**
	 * Jwt sign algo
	 *
	 * @var String $jwt_sign_algo
	 * */
	private $jwt_sign_algo;

	/**
	 * Jwt secret dec
	 *
	 * @var String $jwt_dec_secret
	 * */
	private $jwt_dec_secret;

	/**
	 * Config
	 *
	 * @var Array $attr_config
	 * */
	private $attr_config;

	/**
	 * Username
	 *
	 * @var String $attr_uname
	 * */
	private $attr_uname;

	/**
	 * Email
	 *
	 * @var String $attr_email
	 * */
	private $attr_email;

	/**
	 * First name
	 *
	 * @var String $attr_fname
	 * */
	private $attr_fname;

	/**
	 * Last name
	 *
	 * @var String $attr_lname
	 * */
	private $attr_lname;

	/**
	 * Dname
	 *
	 * @var String $attr_dname
	 * */
	private $attr_dname;

	/**
	 * Jwt version
	 *
	 * @var String $jwt_versi
	 * */
	private $jwt_versi;

	/**
	 * Method configuration
	 *
	 * @var Array $method_config
	 * */
	private $method_config;


	/**
	 * Method selected
	 *
	 * @var bool $is_selected
	 * */
	private $is_selected;


	/**
	 * Current method configuration
	 *
	 * @var Array $method_config
	 * */
	private $current_config;

	/**
	 * Custom token name.
	 *
	 * @var string
	 */
	private $jwt_token_name;

	/**
	 * Checkbox for getting token from headers.
	 *
	 * @var string
	 */
	private $get_jwt_from_header;

	/**
	 * Checkbox for JWT auth for all request.
	 *
	 * @var string
	 */
	private $enable_jwt_auth_on_all_requests;

	/**
	 * Checkbox for SSO Audit Logs.
	 *
	 * @var string
	 */
	private $enable_jwt_audit_logs;

	/**
	 * Addition mapped attributes.
	 *
	 * @var string
	 */
	private $attr_integrator;

	/**
	 * Premium branding label.
	 *
	 * @var string $jwt_label
	 */
	private $jwt_label;

	/**
	 * Premium branding label string.
	 *
	 * @var string $jwt_label
	 */
	private $jwt_label_string;

	/**
	 * Role Mapping settings.
	 *
	 * @var array
	 */
	private $role_config;

	/**
	 * Default Role value.
	 *
	 * @var string
	 */
	private $default_role;

	/**
	 * Role mapping key.
	 *
	 * @var string
	 */
	private $role_attr;

	/**
	 * Role mapping attribute-role pairs.
	 *
	 * @var array
	 */
	private $role_mapping;

	/**
	 * Disable UI based on user license expiry status
	 *
	 * @var $ui_status
	 * */
	private $ui_status;

	/**
	 * Constructor
	 *
	 * @param bool $method_config Config of method.
	 * @param bool $selected_method Selected method.
	 */
	public function __construct( $method_config = false, $selected_method = false ) {
		global $mj_util, $mo_jwt_license_subscription_namespace;
		$this->method_name                     = 'Login User with JWT';
		$this->image_name                      = 'login-user.png';
		$this->method_slug                     = 'jwtlogin';
		$this->priority                        = 4;
		$this->current_config                  = $mj_util->get_plugin_config( 'mo_jwt_config_settings' );
		$this->method_config                   = $this->current_config[ $this->method_slug ];
		$this->inbuilt_secret                  = $this->current_config['jwtcreate']['mo_jwt_dec_secret'];
		$this->jwt_redirection                 = $this->method_config['mo_jwt_redirection'];
		$this->is_selected                     = $selected_method ? ( $selected_method === $this->method_slug ? true : false ) : false;
		$this->get_jwt_from_url                = $this->method_config['mo_jwt_get_token_from_url'];
		$this->jwt_token_name                  = $this->method_config && isset( $this->method_config['mo_jwt_token_name'] ) ? $this->method_config['mo_jwt_token_name'] : 'mo_jwt_token';
		$this->get_jwt_from_cookie             = $this->method_config && isset( $this->method_config['mo_jwt_get_token_from_cookie'] ) ? $this->method_config['mo_jwt_get_token_from_cookie'] : 'off';
		$this->get_jwt_from_header             = $this->method_config && isset( $this->method_config['mo_jwt_get_token_from_header'] ) ? $this->method_config['mo_jwt_get_token_from_header'] : 'off';
		$this->enable_jwt_auth_on_all_requests = $this->method_config && isset( $this->method_config['mo_jwt_enable_auth_on_all_requests'] ) ? $this->method_config['mo_jwt_enable_auth_on_all_requests'] : false;
		$this->enable_jwt_audit_logs           = $this->method_config && isset( $this->method_config['mo_jwt_enable_audit_logs'] ) ? $this->method_config['mo_jwt_enable_audit_logs'] : 'off';
		$this->token_validation_method         = isset( $this->method_config['mo_jwt_token_validation_method'] ) ? $this->method_config['mo_jwt_token_validation_method'] : 'signing_key';
		$this->introspection_endpoint          = $this->method_config && isset( $this->method_config['introspection_endpoint'] ) ? $this->method_config['introspection_endpoint'] : false;
		$this->client_id                       = $this->method_config && isset( $this->method_config['oauth_client_id'] ) ? $this->method_config['oauth_client_id'] : false;
		$this->client_secret                   = $this->method_config && isset( $this->method_config['oauth_client_secret'] ) ? $this->method_config['oauth_client_secret'] : false;
		$this->jwks_endpoint                   = $this->method_config && isset( $this->method_config['jwks_endpoint'] ) ? $this->method_config['jwks_endpoint'] : false;
		$this->jwt_sign_algo                   = $this->method_config && isset( $this->method_config['jwt_sign_algo'] ) ? $this->method_config['jwt_sign_algo'] : false;
		$this->jwt_dec_secret                  = $this->method_config && isset( $this->method_config['jwt_sign_key'] ) ? $this->method_config['jwt_sign_key'] : false;
		$this->attr_config                     = $mj_util->get_plugin_config( 'mo_jwt_attr_settings' ) ? $mj_util->get_plugin_config( 'mo_jwt_attr_settings' ) : false;
		$this->attr_uname                      = $this->attr_config && isset( $this->attr_config['default']['username'] ) ? $this->attr_config['default']['username'] : false;
		$this->attr_email                      = $this->attr_config && isset( $this->attr_config['default']['email'] ) ? $this->attr_config['default']['email'] : false;
		$this->attr_fname                      = $this->attr_config && isset( $this->attr_config['default']['fname'] ) ? $this->attr_config['default']['fname'] : false;
		$this->attr_lname                      = $this->attr_config && isset( $this->attr_config['default']['lname'] ) ? $this->attr_config['default']['lname'] : false;
		$this->attr_dname                      = $this->attr_config && isset( $this->attr_config['default']['dname'] ) ? $this->attr_config['default']['dname'] : false;
		$this->attr_integrator                 = $this->attr_config && isset( $this->attr_config['default']['attr_integrator'] ) ? $this->attr_config['default']['attr_integrator'] : false;
		$this->jwt_versi                       = $mj_util->get_versi();
		$this->jwt_label                       = $mj_util->get_label_icon();
		$this->jwt_label_string                = $mj_util->get_label_string();
		$this->role_config                     = $mj_util->get_plugin_config( 'mo_jwt_role_settings' ) ? ( $mj_util->get_plugin_config( 'mo_jwt_role_settings' ) ) : false;
		$this->default_role                    = $this->role_config && isset( $this->role_config['default']['default_role'] ) ? $this->role_config['default']['default_role'] : false;
		$this->role_attr                       = $this->role_config && isset( $this->role_config['default']['role_attr'] ) ? $this->role_config['default']['role_attr'] : '';
		$this->role_mapping                    = $this->role_config && isset( $this->role_config['default']['role_mapping'] ) ? $this->role_config['default']['role_mapping'] : false;
		$this->ui_status                       = $mj_util->get_versi() && null !== $mo_jwt_license_subscription_namespace ? $mo_jwt_license_subscription_namespace::get_html_disabled_status() : '';
	}

	/**
	 * Method priority to view
	 *
	 * @return string
	 */
	public function get_priority() {
		return $this->priority;
	}

	/**
	 * Method name
	 *
	 * @return string
	 */
	public function get_method_name() {
		return $this->method_name;
	}

	/**
	 * Image of method
	 *
	 * @return string
	 */
	public function get_image_name() {
		return $this->image_name;
	}

	/**
	 * Method slug
	 *
	 * @return string
	 */
	public function get_method_slug() {
		return $this->method_slug;
	}

	/**
	 * JWT allow action
	 *
	 * @return string
	 */
	public function get_allow_jwt_login_action() {
		return $this->jwt_allow_login;
	}

	/**
	 * JWT redirect action
	 *
	 * @return string
	 */
	public function get_jwt_redirection_action() {
		return $this->jwt_redirection;
	}

	/**
	 * Config UI
	 */
	public function load_config_view() {
		?>
		<div style="padding-left: 20px;
		<?php
		if ( ! $this->is_selected ) {
			echo 'display:none;';}
		?>
		" id="<?php echo esc_attr( $this->method_slug . '_div' ); ?>" >
			<br>
			<div class="mo_jwt_method_note">This feature will help you to auto login (Single Sign On) your users in WordPress using the user based JWT token either created from the plugin or obtained from external identities like OAuth 2.0/OpenID Connect providers, Firebase etc. Click on <b><i>Save Settings</i></b> to know more.</div>

			<h3>Get JWT token from: </h3>
			<small>(This setting will allow the plugin to identify from the JWT token needs to be fetched)</small><br><br>
			<table width="60%">
				<tr>
					<td>
						<b>Request URL Parameter:</b> 
					</td>
					<td><select name="mo_jwt_get_token_from_url" style="min-width:80px" <?php echo esc_attr( $this->ui_status ); ?> >
							<option value="on" 
							<?php
							if ( 'on' === $this->get_jwt_from_url ) {
								echo 'selected';}
							?>
							selected>on</option>
							<option value="off" 
							<?php
							if ( 'off' === $this->get_jwt_from_url ) {
								echo 'selected';}
							?>
							>off</option>
					</select>
					</td>
				</tr>
				<tr>
					<td>
						<b>Cookie: <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></b> 
					</td>
					<td><select name="mo_jwt_get_token_from_cookie" style="min-width:80px" 
					<?php
					echo esc_attr( $this->ui_status );
					if ( ! $this->jwt_versi ) {
						echo 'disabled';}
					?>
					>
							<option value="off" 
							<?php
							if ( 'off' === $this->get_jwt_from_cookie ) {
								echo 'selected';}
							?>
							selected>off</option>
							<option value="on"  
							<?php
							if ( 'on' === $this->get_jwt_from_cookie ) {
								echo 'selected';}
							?>
							>on</option>
					</select>
					</td>
				</tr>
				<tr>
					<td>
						<b>Request Header: <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></b> 
					</td>
					<td><select name="mo_jwt_get_token_from_header" style="min-width:80px" onchange="mo_jwt_onchange_header(this)"
					<?php
					echo esc_attr( $this->ui_status );
					if ( ! $this->jwt_versi ) {
						echo 'disabled';}
					?>
					>
							<option value="off" 
							<?php
							if ( 'off' === $this->get_jwt_from_header ) {
								echo 'selected';}
							?>
							selected>off</option>
							<option value="on" 
							<?php
							if ( 'on' === $this->get_jwt_from_header ) {
								echo 'selected';}
							?>
							>on</option>
					</select>
					</td>
				</tr>

				<tr>
					<td>
						<b>JWT Token Name: <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></b> 
					</td>
					<td>
						<h4 class="mo_jwt_attr_h4">
							<input type="text" name="mo_jwt_token_name_temporary" id=mo_jwt_token_name_temporary style="display:none" value="mo-jwt-token" <?php echo esc_attr( $this->ui_status ); ?> />
							<input type="text" name="mo_jwt_token_name" id=mo_jwt_token_name class="jwtlogin_required"  value="<?php echo esc_attr( $this->jwt_token_name ); ?>"
						<?php
						echo esc_attr( $this->ui_status );
						if ( ! $this->jwt_versi ) {
							echo 'disabled';}
						?>
						/></h4>
					</td>
					<td style="width:auto">
						<small>(For request header, token name should only contain alphanumerics and hyphen.)</small>
					</td>
				</tr>
			</table>
			<br>
			<table width="100%">
				<tr>
					<td><input type="checkbox" name="mo_jwt_enable_auth_on_all_requests" 
					<?php
					echo esc_attr( $this->ui_status );
					if ( $this->enable_jwt_auth_on_all_requests ) {
						echo 'checked';}
					?>
					<?php
					if ( ! $this->jwt_versi ) {
						echo 'disabled';}
					?>
					>Enable JWT login flow for already logged-in user. <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></td>
				</tr>
			</table>
			<br>
			<table width="100%">
				<tr>
					<td><input type="checkbox" name="mo_jwt_enable_audit_logs" 
					<?php
					echo esc_attr( $this->ui_status );
					if ( 'on' === $this->enable_jwt_audit_logs ) {
						echo 'checked';}
					?>
					<?php
					if ( ! $this->jwt_versi ) {
						echo 'disabled';}
					?>
					>Enable User Audit on JWT SSO. <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></td>
				</tr>
			</table>
			<br>
			<h3>User Redirection after Auto-login:</h3>
			<table width="120%">
				<tr>
					<td colspan="2">
						<input type="radio" name="mo_jwt_redirection" value="home_redirect" 
						<?php
						echo 'checked ';
						echo esc_attr( $this->ui_status );
						?>
						> Homepage 
					</td>
					<td colspan="2">
						<input type="radio" name="mo_jwt_redirection" value="no_redirect" 
						<?php
						echo esc_attr( $this->ui_status );
						if ( 'no_redirect' === $this->jwt_redirection ) {
							echo 'checked';}
						?>
						> No Redirect (Users will be redirecting on same page from where auto-login is initiated.)
					</td>
				</tr>
			</table>
			<br>
			<h3>JWT token validation Method:</h3>    
			<table width="120%">

				<tr>
					<td colspan="2">
						<input type="radio" name="mo_jwt_login_validation" value="signing_key" id="SHA"
						<?php
						echo esc_attr( $this->ui_status );
						if ( 'signing_key' === $this->token_validation_method ) {
							echo ' checked';}
						?>
						onclick="MoJWTLoadThirdPartyMethod('SHA', 'SHA,OAuth,JWKS')" checked> Signing Key/Certificate Validation
					</td>
					<td colspan="2">
						<input type="radio" name="mo_jwt_login_validation" value="jwks" id="JWKS"
						<?php
						echo esc_attr( $this->ui_status );
						if ( 'jwks' === $this->token_validation_method ) {
							echo 'checked';}
						?>
						onclick="MoJWTLoadThirdPartyMethod('JWKS', 'SHA,OAuth,JWKS')" 
						<?php
						if ( ! $this->jwt_versi ) {
											echo 'disabled';}
						?>
						> JWKS Validation <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small>
					</td>
					<td colspan="2">
						<input type="radio" name="mo_jwt_login_validation" value="oauth_oidc" id="OAuth"
						<?php
						echo esc_attr( $this->ui_status );
						if ( 'oauth_oidc' === $this->token_validation_method ) {
							echo 'checked';}
						?>
						onclick="MoJWTLoadThirdPartyMethod('OAuth', 'SHA,OAuth,JWKS')" 
						<?php
						if ( ! $this->jwt_versi ) {
											echo 'disabled';}
						?>
						> OAuth/OIDC Validation <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small>
					</td>
				</tr>
			</table>
			<br/>
			<section id="SHA_div" 
			<?php
			if ( ! ( 'signing_key' === $this->token_validation_method || false === $this->token_validation_method ) ) {
				echo 'style=" display:none;"';}
			?>
			>
				<p><table width="120%">
				<tr>
					<td>
						<b>Signing Algorithm :</b> 
						<br><small>Select the algorithm using which you want to sign the JWT</small>
					</td>
					<td style="padding-left: 5px;">
						<select name="mo_jwt_login_sign_algo" style="min-width:100px" <?php echo esc_attr( $this->ui_status ); ?>>
							<option value="HS256" 
							<?php
							if ( 'HS256' === $this->jwt_sign_algo ) {
								echo 'selected';}
							?>
							selected>HS256</option>
							<option value="RS256" 
							<?php
							if ( 'RS256' === $this->jwt_sign_algo ) {
								echo 'selected';}
							?>
							<?php
							if ( ! $this->jwt_versi ) {
								echo 'disabled';}
							?>
							>RS256 <small><?php echo ( $this->jwt_label_string ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></option>
						</select>
					</td>
				</tr>
			</table></p>
			<br>
			<table width="120%">
				<tr>
					<td>
						<b>Decryption key/certificate : <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></b> 
						<br><small>Enter the key/certificate to Decrypt the JWT</small>
					</td>
					<td>
						<td><textarea type="textbox" placeholder="Configure your certificate or secret key" name="mo_jwt_login_dec_secret" style="width:350px;" class="SHA_required" 
						<?php
						echo esc_attr( $this->ui_status );
						if ( ! $this->jwt_versi ) {
							echo 'disabled';}
						?>
						><?php // phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentBeforeOpen since it's inside an input text area indenting will add spaces to the input.
						if ( ! $this->jwt_versi ) {
							echo esc_html( $this->inbuilt_secret );
						} elseif ( $this->jwt_dec_secret ) {
							echo esc_html( $this->jwt_dec_secret );}
						// phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentAfterEnd since it's inside an input text area indenting will add spaces to the input.?></textarea>
						</td>
					</td>
				</tr>
			</table>

			</section>
		</br>
			<section id="OAuth_div" 
			<?php
			if ( ! ( 'oauth_oidc' === $this->token_validation_method ) ) {
				echo 'style = "display:none;"';}
			?>
			>
				<p><b>OAuth 2.0 Introspection/Userinfo Endpoint : </b>&nbsp;<br/>
				<small>This endpoint is used to query Third Party OAuth provider to identify if the OAuth token exists and is valid</small><br/>
				<input type="textbox" placeholder="OAuth Introspection endpoint" name="introspection_endpoint" style="width:80%;padding:3px" class="OAuth_required" <?php echo esc_attr( $this->ui_status ); ?> value="<?php // phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentBeforeOpen since it's inside an input text area indenting will add spaces to the input.
				if ( $this->introspection_endpoint ) {
					echo esc_attr( $this->introspection_endpoint );}
				// phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentAfterEnd since it's inside an input text area indenting will add spaces to the input.?>" /></p>

				<p><b>OAuth 2.0 Client ID : </b>&nbsp;<br/>
				<input type="textbox" placeholder="OAuth Client ID" name="client_id" style="width:80%;padding:3px" class="OAuth_required" <?php echo esc_attr( $this->ui_status ); ?> value="<?php // phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentBeforeOpen since it's inside an input text area indenting will add spaces to the input.
				if ( $this->client_id ) {
					echo esc_attr( $this->client_id );}
				// phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentAfterEnd since it's inside an input text area indenting will add spaces to the input.?>" /></p>

				<p><b>OAuth 2.0 Client Secret : </b>&nbsp;<br/>
				<input type="textbox" placeholder="OAuth Client Secret" name="client_secret" class="OAuth_required" style="width:80%;padding:3px" <?php echo esc_attr( $this->ui_status ); ?> value="<?php // phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentBeforeOpen since it's inside an input text area indenting will add spaces to the input.
				if ( $this->client_secret ) {
					echo esc_attr( $this->client_secret );}
				// phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentAfterEnd since it's inside an input text area indenting will add spaces to the input.?>" /></p>
			</section>

			<section id="JWKS_div" 
			<?php
			if ( ! ( 'jwks' === $this->token_validation_method ) ) {
				echo 'style = "display:none;"';}
			?>
			>
				<p><b>JWKS URL : </b>&nbsp;<br/>
				<small>This endpoint is used to create the public keys for the JWT token and validate the signature</small><br/>
				<input type="textbox" placeholder="JWKS endpoint" name="jwks_endpoint" class="JWKS_required" style="width:80%;padding:3px" <?php echo esc_attr( $this->ui_status ); ?> value="<?php // phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentBeforeOpen since it's inside an input text area indenting will add spaces to the input.
				if ( $this->jwks_endpoint ) {
					echo esc_attr( $this->jwks_endpoint );}
				// phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentAfterEnd since it's inside an input text area indenting will add spaces to the input.?>" /></p>
			</section>
		</div>

		<script>
			function MoJWTLoadThirdPartyMethod( section, allsectionid ){
				MoJWThideVisibility( allsectionid );
				document.getElementById( section + "_div" ).style.display = "block";
			}
			function mo_jwt_onchange_header(Flag){
				pattern1 = /^([A-Za-z0-9-]+)+$/; // Correct pattern for jwt header key name.
				user_input =document.getElementById("mo_jwt_token_name"); // User's current input.
				temporary_input_store = document.getElementById("mo_jwt_token_name_temporary"); // Temporary storage for user input
				if( Flag.value == 'on' ) { // If in headers is switched to on.
					if( pattern1.test(user_input.value) ) { // If input is in correct pattern store it in temporary space.
						temporary_input_store.value = user_input.value;
					} else { // Paste default header key 'mo-jwt-token'.
						temp_var = user_input.value
						user_input.value = temporary_input_store.value;
						temporary_input_store.value = temp_var;
					}
					// Set regex for input validation.
					user_input.setAttribute('pattern', '^([A-Za-z0-9-]+)+$');
					user_input.setAttribute('title', 'Only alpha numeric and "-" are allowed');
				} else {
					temp_var = user_input.value;
					if( ! pattern1.test(temporary_input_store.value) ) { //If there is a previously stored user input use that.
						temp_var = temporary_input_store.value;
					}
					if( pattern1.test(user_input.value) ) { // If user entered correct header key before switch store it.
						temporary_input_store.value = user_input.value;
					}
					user_input.value = temp_var;

					// Remove regex for input validation.
					user_input.removeAttribute('pattern');
					user_input.removeAttribute('title');
				}

			}
		</script>


		<?php
	}

	/**
	 * UI loader
	 */
	public function load_doc_view() {
		global $mj_util;
		$this->method_slug    = 'jwtlogin';
		$this->current_config = $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ? ( $mj_util->get_plugin_config( 'mo_jwt_config_settings' ) ) : false;
		$this->method_config  = ( $this->current_config && $this->current_config[ $this->method_slug ] ) ? ( $this->current_config[ $this->method_slug ] ) : false;
		$this->jwt_token_name = $this->method_config && isset( $this->method_config['mo_jwt_token_name'] ) ? $this->method_config['mo_jwt_token_name'] : 'mo_jwt_token';
		?>
		<div class="mo_jwt_support_layout">
			<div id="mo_jwt_login_mapping">
			<div>
				<form name="mo_jwt_mapping_form" method="post" action="">
				<?php wp_nonce_field( 'mo_jwt_mapping_section', 'mo_jwt_mapping_section_nonce' ); ?>
				<input type="hidden" name="option" value="mo_jwt_mapping_section">
				<h3>Attribute Mapping Settings </h3>
				<table class="mo_settings_table">
					<tr>
						<td style="vertical-align:top"><h4 class="mo_jwt_attr_h4" style="margin-top:10px;">Username : <span style="color: red;">(Required)</span></h4></td>
						<td style="vertical-align:top"><h4 class="mo_jwt_attr_h4 mo_jwt_placeholder_thin" style="margin-top:10px;"><input required class="mo_jwt_input" placeholder="JWT Username Attribute" type="text" name="mo_jwt_username_attr" <?php echo esc_attr( $this->ui_status ); ?> value="<?php echo esc_attr( $this->attr_uname ); ?>" ></h4></td>
					</tr>
					<tr>
						<td style="vertical-align:top"><h4 class="mo_jwt_attr_h4">Email : <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></h4></td>
						<td style="vertical-align:top"><h4 class="mo_jwt_attr_h4 mo_jwt_placeholder_thin"><input class="mo_jwt_input" placeholder="JWT Email Attribute" type="text" name="mo_jwt_email_attr" <?php echo esc_attr( $this->ui_status ); ?> value="<?php echo esc_attr( $this->attr_email ); ?>" 
						<?php
						if ( ! $this->jwt_versi ) {
							echo 'disabled';}
						?>
						></h4></td>
					</tr>
					<tr>
						<td style="vertical-align:top"><h4 class="mo_jwt_attr_h4" >FirstName : <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></h4></td>
						<td style="vertical-align:top"><h4 class="mo_jwt_attr_h4 mo_jwt_placeholder_thin" ><input class="mo_jwt_input" placeholder="JWT Firstname Attribute" type="text" name="mo_jwt_fname_attr" <?php echo esc_attr( $this->ui_status ); ?> value="<?php echo esc_attr( $this->attr_fname ); ?>" 
						<?php
						if ( ! $this->jwt_versi ) {
							echo 'disabled';}
						?>
						></h4></td>
					</tr>
					<tr>
						<td style="vertical-align:top"><h4 class="mo_jwt_attr_h4" >LastName : <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></h4></td>
						<td style="vertical-align:top"><h4 class="mo_jwt_attr_h4 mo_jwt_placeholder_thin" ><input class="mo_jwt_input" placeholder="JWT Lastname Attribute" type="text" name="mo_jwt_lname_attr" <?php echo esc_attr( $this->ui_status ); ?> value="<?php echo ( esc_attr( $this->attr_lname ) ); ?>" 
						<?php
						if ( ! $this->jwt_versi ) {
							echo 'disabled';}
						?>
						></h4></td>
					</tr>
					<tr>
						<td style="vertical-align:top"><h4 class="mo_jwt_attr_h4" >DisplayName : <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></h4></td>
						<td style="vertical-align:top"><h4 class="mo_jwt_attr_h4 mo_jwt_placeholder_thin" ><input class="mo_jwt_input" placeholder="JWT Display name Attribute" type="text" name="mo_jwt_dname_attr" <?php echo esc_attr( $this->ui_status ); ?> value="<?php echo esc_attr( $this->attr_dname ); ?>" 
						<?php
						if ( ! $this->jwt_versi ) {
							echo 'disabled';}
						?>
						></h4></td>
					</tr>
					<tr>
						<td style="vertical-align:top"><h4 class="mo_jwt_attr_h4" >Additional Integration key : <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></h4></td>
						<td style="vertical-align:top"><h4 class="mo_jwt_attr_h4 mo_jwt_placeholder_thin" ><input class="mo_jwt_input" placeholder="JWT Custom value Attribute" type="text" name="mo_jwt_integrator_attr" <?php echo esc_attr( $this->ui_status ); ?> value="<?php echo esc_attr( $this->attr_integrator ); ?>" 
						<?php
						if ( ! $this->jwt_versi ) {
							echo 'disabled';}
						?>
						></h4></td>
					</tr>
					<tr>
						<td>
							<br>
						</td>
					</tr>
					<tr>
						<td>
							<button class="button-primary" style="margin-left: 140%;width: 40%;" <?php echo esc_attr( $this->ui_status ); ?>>Save Settings</button>
						</td>
						<br>
					</tr>
				</table>
				</form>
				<br>
				</div>

		</div>
	</div>
	<br>
	<div>
		<div class="mo_jwt_support_layout">
			<div id="mo_jwt_login_mapping">
			<div>
				<form name="mo_jwt_mapping_form" method="post" action="">
				<?php wp_nonce_field( 'mo_jwt_role_mapping_section', 'mo_jwt_role_mapping_section_nonce' ); ?>
				<input type="hidden" name="option" value="mo_jwt_role_mapping_section">
				<h3>Role Mapping Settings <?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></h3>
				<table class="mo_settings_table" id='mo_jwt_role_mapping_table'>
					<tbody>
					<tr>
						<td><h4 class="mo_jwt_attr_h4" style="margin-top:10px;">Default Role: </h4></td>
						<td>
							<br>
							<div style='width: 10px;'>
								<select style="display:block" id="wp_roles_list" name='mo_jwt_role_default_role' 
								<?php
								echo esc_attr( $this->ui_status );
								if ( ! $this->jwt_versi ) {
									echo 'disabled';}
								?>
								>
									<?php
										! empty( $this->default_role ) ? wp_dropdown_roles( $this->default_role ) : wp_dropdown_roles();
									?>
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<td><h4 class="mo_jwt_attr_h4"><br>Role Attribute: </h4></td>
						<td>
							<div style='width: 10px;'>
								<br><input type="text" name="mo_jwt_role_attr" placeholder='Enter Role attribute' value="<?php echo esc_attr( $this->role_attr ); ?>" 
								<?php
								echo esc_attr( $this->ui_status );
								if ( ! $this->jwt_versi ) {
									echo 'disabled';}
								?>
								></h4>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan='2'><br><input type='button' onclick="display_dynamic_role_mapping_config();" value='+ ADD ROLE MAPPING' style='background: none; border: none; font-weight: bold; color: blue; cursor: pointer; font-size: 12px' 
						<?php
						echo esc_attr( $this->ui_status );
						if ( ! $this->jwt_versi ) {
							echo 'disabled';}
						?>
						></td>
					</tr>
					<tr>
					<?php
					if ( empty( $this->role_mapping ) ) {
						?>
						<td><br><input type='text' placeholder='Role Name' name='mo_jwt_role_mapping_name[]'
						<?php
						echo esc_attr( $this->ui_status );
						if ( ! $this->jwt_versi ) {
							echo 'disabled';}
						?>
						/></td>
						<td>
							<br>
							<div style='width: 10px;'>    
								<select id="wp_roles_list" name='mo_jwt_role_mapping_value[]'
								<?php
								echo esc_attr( $this->ui_status );
								if ( ! $this->jwt_versi ) {
									echo 'disabled';}
								?>
								>
									<?php wp_dropdown_roles(); ?>
								</select>
							</div>
						</td>
					</tr>
						<?php
					} else {
						foreach ( $this->role_mapping as $key => $value ) {
							?>
						<tr>
						<td><br><input type='text' placeholder='Role Name' name='mo_jwt_role_mapping_name[]' value="<?php echo esc_attr( $key ); ?>"
							<?php
							echo esc_attr( $this->ui_status );
							if ( ! $this->jwt_versi ) {
								echo 'disabled';}
							?>
							/></td>
						<td>
							<br>
							<div style='width: 10px;'>    
								<select id="wp_roles_list" name='mo_jwt_role_mapping_value[]' <?php echo esc_attr( $this->ui_status ); ?>>
									<?php wp_dropdown_roles( $value ); ?>
								</select>
							</div>
						</td>
						<td><br><button class='button-primary' type='button' id='mo_jwt_remove_field_button' onclick='hide_dynamic_jwt_role_mapping_config(this);' style='font-weight: bold;   font-size: 13px;' <?php echo esc_attr( $this->ui_status ); ?>>&#8722;</button></td>
						</tr>
							<?php
						}
					}
					?>
					   
					</tbody>
				</table>
				<br>
				<button class="button-primary"
				<?php
				echo esc_attr( $this->ui_status );
				if ( ! $this->jwt_versi ) {
					echo 'disabled';}
				?>
				>Save Settings</button>
				</form>
				<br>
				</div>

		</div>
	</div>
	<br>

	<div class="mo_jwt_support_layout">
		<div id="mo_jwt_support_loginjwt" class="mo_jwt_common_div_css">
			<h3 class="mo-jwt-doc-heading">Sample Example to perform auto login using JWT as URL parameter -</h3>
			<div class="mo-jwt-code" style="min-height: 110px;">
				<p>Append the argument <strong style="color: #084ebf"><?php echo esc_html( $this->jwt_token_name ); ?>=&lt;user-jwt-token&gt;</strong>in any URL of WordPress site from where you want users to get auto logged in.</p>
				<br>
			<table width="30%" class="mo_jwt_settings_table">
				<tr class="mo-jwt-table-desc">
					<td>
						Example Request - &nbsp;&nbsp;<?php echo esc_url( get_home_url() ); ?>?<?php echo esc_html( $this->jwt_token_name ); ?>=&lt;user-jwt-token&gt;
					</td>
				</tr>
			</table>
		</div>
		</div>
		<div id="mo_jwt_support_loginjwt" class="mo_jwt_common_div_css">
			<h3 class="mo-jwt-doc-heading">Sample Example to perform auto login using JWT as Cookie - <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></h3>
			<div class="mo-jwt-code" style="min-height: 110px;">
				<p>Set the cookie with name <strong style="color: #084ebf"><?php echo esc_html( $this->jwt_token_name ); ?> and value=&lt;user-jwt-token&gt;</strong> in your root domain for the user to get auto logged in on site access.</p>
				<br>
			<table width="100%" class="mo_jwt_settings_table">
				<tr class="mo-jwt-table-desc">
					<td>
					Sample Code in PHP - setcookie(<?php echo esc_html( $this->jwt_token_name ); ?>, <'jwt-token'>, time() + 60, $path='/');
					</td>
				</tr>
			</table>
		</div>
		</div>
		<div id="mo_jwt_support_loginjwt" class="mo_jwt_common_div_css">
			<h3 class="mo-jwt-doc-heading">Sample Example to perform auto login using JWT as Headers - <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></h3>
			<div class="mo-jwt-code" style="min-height: 78px;">
				<p>Add the header <strong style="color: #084ebf"><?php echo esc_html( $this->jwt_token_name ); ?>=&lt;user-jwt-token&gt;</strong>in any URL request of WordPress site from where you want users to get auto logged in.</p>
		</div>
		</div>
		<br>
		</div>
		<br>
		<script>
			function display_dynamic_role_mapping_config() {
					var $tableBody = jQuery("#mo_jwt_role_mapping_table").find("tbody");
					$trLast = $tableBody.find("tr:last");
					$trNew = $trLast.clone(true);
					var remove_button = $trNew.find('td:eq(2)').html();
					if(remove_button == undefined) {
						$trNew.append("<td><br><button class=\'button-primary\' type=\'button\' id=\'mo_jwt_remove_field_button\' onclick=\'hide_dynamic_jwt_role_mapping_config(this)\' style='font-weight: bold;   font-size: 13px;'>&#8722;</button></td>");  
					}                        
					$trLast.after($trNew);
				}
				function hide_dynamic_jwt_role_mapping_config(el) {
					jQuery(el).parent().parent().remove();
				}
		</script>
		<?php
	}

}
