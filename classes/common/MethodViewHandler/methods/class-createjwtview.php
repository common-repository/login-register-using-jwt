<?php
/**
 * Core
 *
 * Create JWT Method view Handler.
 *
 * @category   Common, Core
 * @package    MoJWT\MethodViewHandler
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT\MethodViewHandler;

/**
 * Class to Create JWT Method View Handler.
 *
 * @category Common, Core
 * @package  MoJWT\MethodViewHandler\CreateJWTView
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class CreateJWTView {

	/**
	 * Method name
	 *
	 * @var String $method_name
	 * */
	private $method_name;

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
	 * Method configuration
	 *
	 * @var Array $method_config
	 * */
	private $method_config;

	/**
	 * Token expiry
	 *
	 * @var time $token_expiry
	 * */
	private $token_expiry;

	/**
	 * Get jwt version
	 *
	 * @var String $jwt_versi
	 * */
	private $jwt_versi;

	/**
	 * Jwt secret dec
	 *
	 * @var String $jwt_dec_secret
	 * */
	private $jwt_dec_secret;

	/**
	 * Jwt secret
	 *
	 * @var string $jwt_secret
	 * */
	private $jwt_secret;

	/**
	 * JWT sign algo
	 *
	 * @var string $jwt_sign_algo
	 * */
	private $jwt_sign_algo;

	/**
	 * To check method selected
	 *
	 * @var bool $is_selected
	 * */
	private $is_selected;

	/**
	 * Current method configuration
	 *
	 * @var Array $current_config
	 * */
	private $current_config;

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
	 * JWT token invalidate checkbox.
	 *
	 * @var string $jwt_invalidate
	 */
	private $jwt_invalidate;

	/**
	 * Disable UI based on license expiry date of user
	 *
	 * @var string $ui_status
	 */
	private $ui_status;

	/**
	 * Constructor
	 *
	 * @param bool $method_config Config of method.
	 * @param bool $selected_method Selected method.
	 */
	public function __construct( $method_config = false, $selected_method = false ) {
		global $mj_util, $mo_jwt_license_subscription_namespace;
		$this->method_name      = 'Create JWT';
		$this->image_name       = 'jwt-token.png';
		$this->method_slug      = 'jwtcreate';
		$this->priority         = 1;
		$this->current_config   = $mj_util->get_plugin_config( 'mo_jwt_config_settings' );
		$this->method_config    = $this->current_config[ $this->method_slug ];
		$this->is_selected      = $selected_method ? ( $selected_method === $this->method_slug ? true : false ) : false;
		$this->jwt_sign_algo    = $this->method_config['mo_jwt_sign_algo'];
		$this->jwt_secret       = $this->method_config['mo_jwt_secret'];
		$this->jwt_dec_secret   = $this->method_config['mo_jwt_dec_secret'];
		$this->token_expiry     = $this->method_config['mo_jwt_token_expiry'];
		$this->jwt_versi        = $mj_util->get_versi();
		$this->jwt_label        = $mj_util->get_label_icon();
		$this->jwt_label_string = $mj_util->get_label_string();
		$this->jwt_invalidate   = $this->method_config && isset( $this->method_config['mo_jwt_invalidate'] ) ? $this->method_config['mo_jwt_invalidate'] : false;
		$this->jwt_invalidate   = $this->method_config && isset( $this->method_config['mo_jwt_invalidate'] ) ? $this->method_config['mo_jwt_invalidate'] : false;
		$this->ui_status        = $mj_util->get_versi() && null !== $mo_jwt_license_subscription_namespace ? $mo_jwt_license_subscription_namespace::get_html_disabled_status() : '';
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
	 * Config UI
	 */
	public function load_config_view() {
		global $mj_util;
		?>
		<div 
		<?php
		if ( ! $this->is_selected ) {
			echo 'style = "display:none;" ';}
		?>
		id="<?php echo esc_attr( $this->method_slug . '_div' ); ?>" >
			<br>
			<div class="mo_jwt_method_note">This feature will help you to create the JWT token based on WordPress user credentials. This feature also helps you authenticate your users on other app trying to login using WordPress credentials. Click on <b><i>Save Settings</i></b> to know more.</div>

			<h3>JWT Security Settings</h3>
			<table width="120%">
				<tr>
					<td>
						<b>Signing Algorithm :</b> 
						<br><small>Select the algorithm using which you want to sign the JWT</small>
					</td>
					<td style="padding-left: 5px;">
						<select name="mo_jwt_sign_algo" style="min-width:200px" <?php echo esc_attr( $this->ui_status ); ?>>
							<option value="HS256" 
							<?php
							if ( 'HS256' === $this->jwt_sign_algo ) {
								echo 'selected';}
							?>
							selected>HS256</option>
							<option value="RS256" 
							<?php
							if ( 'RS256' === $this->jwt_sign_algo ) {
								echo 'selected';
							} if ( ! $this->jwt_versi ) {
								echo 'disabled';}
							?>
							>RS256&nbsp;&nbsp;<small><?php echo ( $this->jwt_label_string ); // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin. ?></small></option>
						</select>
					</td>
				</tr>
			</table>
			<br>
			<table width="120%">
				<tr>
					<td>
						<b>Signing key/certificate : <small><?php echo ( $this->jwt_label ); // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin. ?></small></b> 
						<br><small>Enter the signing key/certificate to sign the JWT</small>
					</td>
					<td>
						<td><textarea type="textbox" placeholder="Configure your certificate or secret key" name="mo_jwt_secret" style="width:350px;" class="jwtcreate_required"
						<?php
						echo esc_attr( $this->ui_status );
						if ( ! $this->jwt_versi ) {
							echo 'readonly';}
						?>
						><?php // phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentBeforeOpen since it's inside an input text area indenting will add spaces to the input.
							echo esc_html( $this->jwt_secret );
						// phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentAfterEnd since it's inside an input text area indenting will add spaces to the input.?></textarea>
						</td>
					</td>
				</tr>
			</table>
			<br>
			<table width="120%">
				<tr>
					<td>
						<b>Decryption key/certificate : <small><?php echo ( $this->jwt_label ); // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin. ?></small></b> 
						<br><small>Enter the key/certificate to Decrypt the JWT</small>
					</td>
					<td>
						<td><textarea type="textbox" placeholder="Configure your certificate or secret key" name="mo_jwt_dec_secret" style="width:350px;" class="jwtcreate_required" 
						<?php
						echo esc_attr( $this->ui_status );
						if ( ! $this->jwt_versi ) {
							echo 'readonly';}
						?>
						><?php // phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentBeforeOpen since it's inside an input text area indenting will add spaces to the input.
							echo esc_html( $this->jwt_dec_secret );
						// phpcs:ignore --Squiz.PHP.EmbeddedPhp.ContentAfterEnd since it's inside an input text area indenting will add spaces to the input.?></textarea>
						</td>
					</td>
				</tr>
			</table>
			<br>
			<table width="120%">
				<tr>
					<td><b>Access Token Expiry Time (In minutes) : <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></b></td>
					<td style="padding-left: 5px;">
						<input type="text" id="mo_jwt_token_expiry" placeholder="JWT Token Expiry Time (In minutes)" name="mo_jwt_token_expiry" value="<?php echo esc_attr( $this->token_expiry ); ?>" 
						<?php
						echo esc_attr( $this->ui_status );
						if ( ! $this->jwt_versi ) {
							echo 'disabled';}
						?>
						/>
					</td>
				</tr>
			</table>
			<br>
			<table width="120%">
				<tr>
					<td><input type="checkbox" name="mo_jwt_invalidate" 
					<?php
					if ( $this->jwt_invalidate ) {
						echo 'checked';}
					?>
					<?php
					echo esc_attr( $this->ui_status );
					if ( ! $this->jwt_versi ) {
						echo 'disabled';
					}
					?>
						><b>Invalidate existing JWT</b> (This feature will help you invalidate the existing JWT for a user whenever you generate a new JWT)<small style="font-weight: 600;"> <?php echo ( $this->jwt_label ); // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin. ?></small></td>
				</tr>
			</table>
			<br>
			</div>	
		</div>

		<?php
	}

	/**
	 * UI loader
	 */
	public function load_doc_view() {
		?>
		<div class="mo_jwt_support_layout">
			<div id="mo_jwt_support_createjwt" class="mo_jwt_common_div_css">
			<table width="150%">
				<tr>
					<td>
						<b> <h3 class="mo-jwt-doc-heading">Create JWT using the following API endpoint: </h3></b>
					</td>
				</tr>
			</table>
			<br>
			<div class="mo-jwt-code">
				<button class="mo-jwt-api-button">POST</button> /wp-json/api/v1/mo-jwt
			</div>
			<br>
			<div class="mo-jwt-code" style="height: 150px;">	
			<table width="100%">
				<tr class="mo-jwt-table-heading">
					<td>
						Parameter
					</td>
					<td>
						Description
					</td>
				</tr>
				<tr>
					<td><hr style="width: 170%"></td>
				</tr>
				<tr class="mo-jwt-table-desc">
					<td>username</td>
					<td><i style="color: red;">(Required)</i> The WordPress username or email of the user</td>
				</tr>
				<tr>
					<td><br></td>
				</tr>
				<tr class="mo-jwt-table-desc">
					<td>password</td>
					<td><i style="color: red;">(Required)</i> The WordPress password associated for the user</td>
				</tr>
			</table>
		</div>
		<br>
		<div id="mo_jwt_support_createjwt" class="mo_jwt_common_div_css">
			<h2 class="mo-jwt-doc-heading">Sample Example to request the user based JWT</h2>
			<br>
			<div class="mo-jwt-code" style="min-height: 100px;">
				<table width="100%" class="mo_jwt_settings_table">
				<tr class="mo-jwt-table-heading">
					<td>
						Request
					</td>
					<td>
						Format
					</td>
				</tr>
				<tr>
					<td><hr style="width: 170%"></td>
				</tr>
				<tr class="mo-jwt-table-desc">
					<td>Curl</td>
					<td class="mo-jwt-doc-body">curl -d "username=&lt;wordpress_username&gt;&password=&lt;wordpress_password&gt;" -X POST <?php echo esc_url( get_home_url() ); ?>/wp-json/api/v1/mo-jwt</td>
				</tr>
			</table>
		</div>
		<br>
		<h2 class="mo-jwt-doc-heading">Response Codes</h2>
		<div class="mo-jwt-code" style="min-height: 170px;">
			<table width="100%" class="mo_jwt_settings_table">
				<tr class="mo-jwt-table-heading">
					<td>
						Code
					</td>
					<td>
						Description
					</td>
				</tr>
				<tr>
					<td><hr style="width: 280%"></td>
				</tr>
				<tr class="mo-jwt-table-desc">
					<td>200</td>
					<td class="mo-jwt-doc-body"> Successful Response - JWT is created successfully</td>
				</tr>
				<tr>
					<td><hr style="width: 280%"></td>
				</tr>
				<tr class="mo-jwt-table-desc">
					<td>400</td>
					<td class="mo-jwt-doc-body"> Invalid Credentials - Invalid username or password.</td>
				</tr>
				<tr>
					<td><hr style="width: 280%"></td>
				</tr>
				<tr class="mo-jwt-table-desc">
					<td>403</td>
					<td class="mo-jwt-doc-body"> Forbidden - Username and password are required.</td>
				</tr>
			</table>
		</div>
			</div>
		</div>

		<?php
	}

}
