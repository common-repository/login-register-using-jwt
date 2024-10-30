<?php
/**
 * Support
 *
 * JWT Plugin Support.
 *
 * @category   Common
 * @package    MoJWT
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT;

/**
 * Class to Handle and render support form.
 *
 * @category Core
 * @package  MoJWT
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class Support {

	/**
	 * Public function.
	 */
	public static function support() {
		self::support_page();
	}

	/**
	 * Private function to render support form.
	 */
	private static function support_page() {
		global $mj_util;
		?>
		<div id="mo_jwt_support_layout" class="mo_jwt_support_layout">
			<div>
				<h3>Contact Us</h3>
				<p>Need any help? Couldn't find an answer in <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'faq' ), isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' ) ); ?>">FAQ</a>?<br>Just send us a query so we can help you.</p>
				<form method="post" action="">
					<input type="hidden" name="option" value="mo_jwt_contact_us_query_option" />
					<?php wp_nonce_field( 'mo_jwt_contact_us_query_option', 'mo_jwt_contact_us_query_option_nonce' ); ?>
					<table class="mo_settings_table">
						<tr>
							<td><input type="email" class="mo_table_textbox" required name="mo_jwt_contact_us_email" placeholder="Enter email here"
							value="<?php echo esc_attr( $mj_util->mo_jwt_get_option( 'mo_jwt_admin_email' ) ); ?>"></td>
						</tr>
						<tr>
							<td><input type="tel" id="contact_us_phone" pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" placeholder="Enter phone here" class="mo_table_textbox" name="mo_jwt_contact_us_phone" value="<?php echo esc_attr( $mj_util->mo_jwt_get_option( 'mo_jwt_admin_phone' ) ); ?>"></td>
						</tr>
						<tr>
							<td><textarea class="mo_table_textbox" onkeypress="mo_jwt_valid_query(this)" placeholder="Enter your query here" onkeyup="mo_jwt_valid_query(this)" onblur="mo_jwt_valid_query(this)" required name="mo_jwt_contact_us_query" rows="4" style="resize: vertical;"></textarea></td>
						</tr>
					</table>
					<div style="text-align:left;">
						<input type="submit" name="submit" style="margin-top:15px; width:100px;" class="button button-primary button-large" />
					</div>
					<p>If you want custom features in the plugin, just drop an email at <a href="mailto:apisupport@xecurify.com">apisupport@xecurify.com</a>.</p>
				</form>
			</div>
		</div>
		<script>
			jQuery("#contact_us_phone").intlTelInput();
			function mo_jwt_valid_query(f) {
				!(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
						/[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
			}
		</script>
		<?php
	}
}
