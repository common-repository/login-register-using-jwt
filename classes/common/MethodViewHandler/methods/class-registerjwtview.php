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
 * Class to Register JWT Method View Handler.
 *
 * @category Common, Core
 * @package  MoJWT\MethodViewHandler\RegisterJWTView
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class RegisterJWTView {

	/**
	 * Method name
	 *
	 * @var String $method_name
	 * */
	private $method_name;

	/**
	 * Allowed role
	 *
	 * @var string
	 */
	private $allow_role;

	/**
	 * Get jwt version
	 *
	 * @var String $jwt_versi
	 * */
	private $jwt_versi;

	/**
	 * Default role
	 *
	 * @var string
	 */
	private $default_role;

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
	 * Premium branding label.
	 *
	 * @var string $jwt_label
	 */
	private $jwt_label;

	/**
	 * API Key for JWT User Registeration.
	 *
	 * @var string $api_key
	 */
	private $api_key;

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
		$this->method_name    = 'Register User for JWT';
		$this->image_name     = 'add-user.png';
		$this->method_slug    = 'jwtregister';
		$this->priority       = 2;
		$this->current_config = $mj_util->get_plugin_config( 'mo_jwt_config_settings' );
		$this->method_config  = $this->current_config[ $this->method_slug ];
		$this->is_selected    = $selected_method ? ( $selected_method === $this->method_slug ? true : false ) : false;
		$this->default_role   = $this->method_config['mo_jwt_register_role'];
		$this->allow_role     = $this->method_config['mo_jwt_allow_role'];
		$this->api_key        = isset( $this->method_config['mo_jwt_api_key'] ) ? $this->method_config['mo_jwt_api_key'] : '';
		$this->jwt_versi      = $mj_util->get_versi();
		$this->jwt_label      = $mj_util->get_label_icon();
		$this->ui_status      = $mj_util->get_versi() && null !== $mo_jwt_license_subscription_namespace ? $mo_jwt_license_subscription_namespace::get_html_disabled_status() : '';

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
		<div style="padding-left: 15px;
		<?php
		if ( ! $this->is_selected ) {
			echo 'display:none;';}
		?>
		" id="<?php echo esc_attr( $this->method_slug . '_div' ); ?>" >
			<br>
			<div class="mo_jwt_method_note">This feature will help you to create the user in WordPress via API and returns the user based JWT token which can be used further for login, deletion etc. Click on <b><i>Save Settings</i></b> to know more.</div>

			<div>
				<h3>Role Mapping Settings:</h3>
				<table width="120%">
					<tr>
						<td>
							<b>Select Default Role :</b> 
							<br><small>Select the default role for user </small><small style="font-weight: 600;">&nbsp;<?php echo ( $this->jwt_label ); // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin. ?></small>
						</td>
						<td><select name="mo_jwt_register_role" style="min-width:200px" 
						<?php
						echo esc_attr( $this->ui_status );
						if ( ! $this->jwt_versi ) {
							echo 'disabled';}
						?>
						>
						<?php wp_dropdown_roles( $this->default_role ); ?>
						</select>
						</td>
					</tr>
				</table>
				<br>
				<table width="120%">
					<tr>
						<td>
							<b>Allow 'role' parameter in Register request : <small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></b> 
						</td>
						<td><input type="checkbox" name="mo_jwt_allow_role" 
						<?php
						if ( $this->allow_role ) {
							echo 'checked';}
						?>
						<?php
						echo esc_attr( $this->ui_status );
						if ( ! $this->jwt_versi ) {
							echo 'disabled';}
						?>
						>
						</td>
					</tr>
				</table>
				<br>
			</div>
			<div>
				<h3>API Key:</h3>
				<table width="120%">
					<tr>
						<td>
							<b>API Key:</b> 
						</td>
						<td><input type="text" id="mo_jwt_api_key" name="mo_jwt_api_key" value="<?php echo esc_attr( $this->api_key ); ?>" readonly>
							<button type="button" class="button button-secondary" onclick="MoJWTcopyApiKey()" <?php echo esc_attr( $this->ui_status ); ?>>Copy</button>
							<?php
							if ( $this->jwt_versi ) {
								?>
								<button type="button" class="button button-secondary" onclick="MoJWTgenerateNewApiKey()" <?php echo esc_attr( $this->ui_status ); ?>>Generate New Token</button>
								<?php
							}
							?>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<script>
			function MoJWTgenerateNewApiKey(){
				var data = {
					"action": "mo_jwt_generate_new_api_key",
				}

				jQuery.post(ajaxurl, data)
				.done(function(response) {
					document.getElementById("mo_jwt_api_key").value = response.new_api_key;
				});
			}

			function MoJWTcopyApiKey(){
				var apiKey = document.getElementById("mo_jwt_api_key").value;
				navigator.clipboard.writeText(apiKey);
			}
		</script>

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
						<b> <h3 class="mo-jwt-doc-heading">Register user using the following API endpoint: </h3></b>
					</td>
				</tr>
			</table>
				<br>
				<div class="mo-jwt-code">
					<button class="mo-jwt-api-button">POST</button> /wp-json/api/v1/mo-jwt-register
				</div>
				<br>
				<div class="mo-jwt-code" style="height: 220px;">
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
					<td>apikey</td>
					<td><i style="color: red;">(Required)</i> The API Key as configured in the plugin settings <span style="color: red;"></span></td>
				</tr>
				<tr>
					<td><br></td>
				</tr>
				<tr class="mo-jwt-table-desc">
					<td>password</td>
					<td><i style="color: red;">(Optional)</i> The WordPress password associated for the user</td>
				</tr>
				<tr>
					<td><br></td>
				</tr>
				<tr class="mo-jwt-table-desc">
					<td>role</td>
					<td><i style="color: red;">(Optional)</i> The WordPress role to be assigned to the user <span style="color: red;"><small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></span></td>
				</tr>
			</table>
		</div>
		<br>
		<div id="mo_jwt_support_createjwt" class="mo_jwt_common_div_css">
			<h2 class="mo-jwt-doc-heading">Sample Example to request the user registration</h2>
			<br>
			<div class="mo-jwt-code" style="min-height: 100px;">
				<table width="100%" class="mo_jwt_curl_settings_table">
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
					<td class="mo-jwt-doc-body">curl -d "username=&lt;wordpress_username&gt;&password=&lt;wordpress_password&gt;" -X POST <?php echo esc_url( get_home_url() ); ?>/wp-json/api/v1/mo-jwt-register</td>
				</tr>
			</table>
		</div>
		<br>
		<h2 class="mo-jwt-doc-heading">Response Codes</h2>
		<div class="mo-jwt-code" style="min-height: 170px;">
			<table width="100%" class="mo_jwt_curl_settings_table">
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
					<td class="mo-jwt-doc-body"> Successful Response - User is registered successfully and JWT token is sent in the response</td>
				</tr>
				<tr>
					<td><hr style="width: 280%"></td>
				</tr>
				<tr class="mo-jwt-table-desc">
					<td>400</td>
					<td class="mo-jwt-doc-body"> Bad Request - Pass the username and password in the request body</td>
				</tr>
				<tr>
					<td><hr style="width: 280%"></td>
				</tr>
				<tr class="mo-jwt-table-desc">
					<td>403</td>
					<td class="mo-jwt-doc-body"> Forbidden - Username or API key is required.</td>
				</tr>
			</table>
		</div>
			</div>
		</div>

		<?php
	}

}
