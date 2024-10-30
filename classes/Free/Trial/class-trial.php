<?php
/**
 * Plugin
 *
 * JWT Login Trial Page.
 *
 * @category   Core
 * @package    MoJWT
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT\Free;

/**
 * Class to Handle Trial page UI and functions.
 *
 * @category Core
 * @package  MoJWT
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class Trial {
	/**
	 * Renders UI for trials tab.
	 *
	 * @return void
	 */
	public function render_ui() {
		global $mj_util;
		if ( $mj_util->mo_jwt_get_option( 'mo_jwt_demo_creds' ) ) {
			$demo_credentials   = get_option( 'mo_jwt_demo_creds' );
			$site_url           = isset( $demo_credentials['site_url'] ) ? $demo_credentials['site_url'] : '';
			$email              = isset( $demo_credentials['email'] ) ? $demo_credentials['email'] : '';
			$temporary_password = isset( $demo_credentials['temporary_password'] ) ? $demo_credentials['temporary_password'] : '';
			$password_link      = isset( $demo_credentials['password_link'] ) ? $demo_credentials['password_link'] : '';
			$validity           = isset( $demo_credentials['validity'] ) ? $demo_credentials['validity'] : '';
			?>
			<div class="mo_jwt_support_layout">
				<p style="font-size: 14px;font-weight: 400">You have successfully availed the trial for the <i>full featured</i> <b>Premium plugin</b>. Please find the details below.</p>
				<table width="50%">
					<tr>
						<td>
							<p style="font-size: 15px;font-weight: 500;margin-left: 20px">Trial URL : </p>
						</td>
						<td>
							<p><a href="<?php echo esc_url( $site_url . '/admin.php?page=mo_jwt_settings' ); ?>" target="_blank"><b>[Click Here]</b></a>
						</td>
					</tr>
					<tr>
						<td>
							<p style="font-size: 15px;font-weight: 500;margin-left: 20px">Username : </p>
						</td>
						<td>
							<p><?php echo esc_html( $email ); ?></p>
						</td>
					</tr>
					<tr>
						<td>
							<p style="font-size: 15px;font-weight: 500;margin-left: 20px">Password : </p>
						</td>
						<td>
							<p>
								<?php echo esc_html( $temporary_password ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<td>
							<p style="font-size: 15px;font-weight: 500;margin-left: 20px">Valid Till: </p>
						</td>
						<td>
							<p>
								<?php echo esc_html( $validity ); ?>
							</p>
						</td>
					</tr>
				</table>
				<p style="font-size: 14px;font-weight: 400;padding-left:20px">You can also reset your trial password using this <a href="<?php echo esc_url( $password_link ); ?>" target="_blank"><b>[LINK]</b></a>.</p>
				<p><b>Tip:</b> You must have received an email as well for these credentials to access this trial. Also, if you face any issues or still not convinced with this trial, don't hesitate to contact us at <b><a href="mailto:apisupport@xecurify.com?subject=WP Login & Register using JWT Plugin - Enquiry">apisupport@xecurify.com</a></b>.</p>
			</div>
		<?php } else { ?>
		<div class="mo_jwt_support_layout">
			<div class="mo_jwt_settings_table">
				<h1><b>Demo/Trial Request for Premium Plan</b></h1>
				<p>Make a request for the demo/trial of the Premium plan of the plugin to try all the features.</p>
				<form method="POST" action="">
					<br>
					<input type="hidden" name="option" value="mo_jwt_demo_request" />
					<?php wp_nonce_field( 'mo_jwt_demo_request', 'mo_jwt_demo_request_nonce' ); ?>
						<table width="100%">
							<tr>
								<td>
									<p><b>Email: </b></p>
								</td>
								<td>
									<p><input required type="text" style="width: 95%" name="mo_jwt_demo_email" placeholder="person@example.com" value="<?php echo esc_attr( get_option( 'mo_jwt_admin_email' ) ); ?>">
								</td>
							</tr>
							<tr>
							<tr>
								<td>
									<p><b>JWT Operations: </b></p>
								</td>
								<td>
									<p>
										<p><input type="checkbox" name="mo_jwt_demo_login_with_jwt">Login User with JWT
										<p><input type="checkbox" name="mo_jwt_demo_register_with_jwt">Register User with JWT
										<p><input type="checkbox" name="mo_jwt_demo_delete_with_jwt">Delete User with JWT
									</p>
								</td>
							</tr>
							<tr>
								<td>
									<p><b>Use Case and Requirements: </b></p>
								</td>
								<td>
									<p>
										<textarea type="text" minlength="15" name="mo_jwt_demo_usecase" style="resize: vertical; width:95%; height:100px;" rows="4" placeholder="Write about your usecase" required value=""></textarea>
									</p>
								</td>
							</tr>
							<tr>
								<td></td>
								<td><button type="submit" name="submit" class="button button-primary button-large">Generate Trial</button></td>
							</tr>
						</table>
						<br>                    
					<p><b>Tip:</b> You will receive the email shortly with the demo details once you successfully make the demo/trial request. If not received, please check out your spam folder or contact us at <a href="mailto:apisupport@xecurify.com?subject=WP Login & Register using JWT Plugin - Enquiry">apisupport@xecurify.com</a>.</p><br>
				</form>
			</div>
		</div>
			<?php
		}
	}
}
