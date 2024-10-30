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
 * Class to Delete JWT Method View Handler.
 *
 * @category Common, Core
 * @package  MoJWT\MethodViewHandler\DeleteJWTView
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class DeleteJWTView {

	/**
	 * Method name
	 *
	 * @var string $method_name
	 * */
	private $method_name;

	/**
	 * To check method selected
	 *
	 * @var bool $is_selected
	 * */
	private $is_selected;

	/**
	 * Current method configuration
	 *
	 * @var array $current_config
	 * */
	private $current_config;

	/**
	 * Image of method
	 *
	 * @var string $image_name
	 * */
	private $image_name;

	/**
	 * Method slug
	 *
	 * @var string $method_slug
	 * */
	private $method_slug;

	/**
	 * Method priority to view
	 *
	 * @var string $priority
	 * */
	private $priority;

	/**
	 * Method configuration
	 *
	 * @var array $method_config
	 * */
	private $method_config;

	/**
	 * Premium label.
	 *
	 * @var string $jwt_label
	 * */
	private $jwt_label;

	/**
	 * Constructor
	 *
	 * @param bool $method_config Config of method.
	 * @param bool $selected_method Selected method.
	 */
	public function __construct( $method_config = false, $selected_method = false ) {
		global $mj_util;
		$this->method_name    = 'Delete User with JWT';
		$this->image_name     = 'remove-user.png';
		$this->method_slug    = 'jwtdelete';
		$this->priority       = 3;
		$this->current_config = $mj_util->get_plugin_config( 'mo_jwt_config_settings' );
		$this->method_config  = $this->current_config[ $this->method_slug ];
		$this->is_selected    = $selected_method ? ( $selected_method === $this->method_slug ? true : false ) : false;
		$this->jwt_label      = $mj_util->get_label_icon();
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
			<div class="mo_jwt_method_note">This feature will help you to delete your users from WordPress via API endpoint using the user based JWT token. Click on <b><i>Save Settings</i></b> to know more.</div>
			<br>
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
					<b> <h3 class="mo-jwt-doc-heading">Delete JWT using the following API endpoint: </h3></b>
					</td>
				</tr>
			</table>
			<br>
			<div class="mo-jwt-code">
				<button class="mo-jwt-api-button">POST</button> /wp-json/api/v1/mo-jwt-delete
			</div>
			<br>
			<div class="mo-jwt-code" style="height: 130px;">
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
					<td>jwt-token</td>
					<td><i style="color: red;">(Required)</i> The JWT token of the WordPress user</td>
				</tr>
				<tr>
					<td><br></td>
				</tr>
				<tr class="mo-jwt-table-desc">
					<td>apikey</td>
					<td><i style="color: red;">(Optional)</i> The API Key as configured in the plugin settings <span style="color: red;"><small><?php echo ( $this->jwt_label ) // phpcs:ignore -- WordPress.Security.EscapeOutput.OutputNotEscaped value is hardcoded html string in the plugin.; ?></small></span></td>
				</tr>
			</table>
		</div>
		<br>
		<div id="mo_jwt_support_createjwt" class="mo_jwt_common_div_css">
			   
			<h2 class="mo-jwt-doc-heading">Sample Example to request the user based JWT</h2>
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
					<td class="mo-jwt-doc-body">curl -d "jwt-token=&lt;JWT_token&gt;" -X POST <?php echo esc_url( get_home_url() ); ?>/wp-json/api/v1/mo-jwt-delete</td>
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
					<td class="mo-jwt-doc-body"> Successful Response - User deleted successfully</td>
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
					<td class="mo-jwt-doc-body"> Unauthorized - Username or password passed in the request is incorrect</td>
				</tr>
			</table>
		</div>
			</div>
		</div>

		<?php
	}

}
