<?php
/**
 * App
 *
 * JWT Login Feedback Form.
 *
 * @category   Free
 * @package    MoJWT
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT\Free;

/**
 * Class to Render Feedback Form.
 *
 * @category Core
 * @package  MoJWT
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class Feedback {

	/**
	 * Function to show form to user.
	 */
	public function show_form() {
		global $mj_util;

		$path = isset( $_SERVER['PHP_SELF'] ) ? sanitize_text_field( wp_unslash( $_SERVER['PHP_SELF'] ) ) : '';
		if ( 'plugins.php' !== basename( $path ) ) {
			return;
		}
		$this->enqueue_styles();
		if ( 'FREE' === $mj_util->get_versi_str() ) {
			$this->render_feedback_form();
		}
	}

	/**
	 * Function to enqueue required css/js.
	 */
	private function enqueue_styles() {
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_script( 'utils' );
		wp_enqueue_style( 'mo_jwt_feedback_style', MJ_URL . 'classes/Free/resources/feedback.min.css', array(), $ver = null, $in_footer = false );
	}
	/**
	 * Function to render feedback form.
	 */
	private function render_feedback_form() {
		?>
		<div id="mo_jwt_feedback_modal" class="mo_jwt_modal">
			<div class="mo_jwt_modal-content">
				<span class="mo_jwt_close">&times;</span>
				<h3>Tell us what happened? </h3>
				<form name="f" method="post" action="" id="mo_jwt_feedback">
					<input type="hidden" name="option" value="mo_jwt_feedback"/>
					<?php wp_nonce_field( 'mo_jwt_feedback', 'mo_jwt_feedback_nonce' ); ?>
					<div>
						<p style="margin-left:2%">
						<?php $this->render_radios(); ?>
						<br>
						<textarea id="mo_jwt_query_feedback" name="mo_jwt_query_feedback" rows="4" style="margin-left:2%;width: 330px"
								placeholder="Write your query here"></textarea>
						<br><br>
						<div class="mo_jwt_modal-footer">
							<input type="submit" name="miniorange_jwt_feedback_submit"
								class="button button-primary button-large" style="float: left;" value="Submit"/>
							<input id="mo_jwt_skip" type="submit" name="miniorange_jwt_feedback_skip"
								class="button button-primary button-large" style="float: right;" value="Skip"/>
						</div>
					</div>
				</form>
				<form name="f" method="post" action="" id="mo_jwt_feedback_form_close">
					<input type="hidden" name="option" value="mo_jwt_skip_feedback"/>
					<?php wp_nonce_field( 'mo_jwt_skip_feedback', 'mo_jwt_skip_feedback_nonce' ); ?>
				</form>
			</div>
		</div>
		<?php
		$this->emit_script();
	}

	/**
	 * Function to emit JS.
	 */
	private function emit_script() {
		?>
		<script>
			jQuery('a[aria-label="Deactivate WP Login & Register using JWT"]').click(function () {
				var mo_modal = document.getElementById('mo_jwt_feedback_modal');
				var mo_skip = document.getElementById('mo_jwt_skip');
				var span = document.getElementsByClassName("mo_jwt_close")[0];
				mo_modal.style.display = "block";
				jQuery('input:radio[name="mo_jwt_deactivate_reason_radio"]').click(function () {
					var reason = jQuery(this).val();
					var query_feedback = jQuery('#mo_jwt_query_feedback');
					query_feedback.removeAttr('required')
					if (reason === "Does not have the features I'm looking for") {
						query_feedback.attr("placeholder", "Let us know what feature are you looking for");
					} else if (reason === "Other Reasons:") {
						query_feedback.attr("placeholder", "Can you let us know the reason for deactivation");
						query_feedback.prop('required', true);
					} else if (reason === "Bugs in the plugin") {
						query_feedback.attr("placeholder", "Can you please let us know about the bug in detail?");
					} else if (reason === "Confusing Interface") {
						query_feedback.attr("placeholder", "Finding it confusing? let us know so that we can improve the interface");
					} else if (reason === "Endpoints not available") {
						query_feedback.attr("placeholder", "We will send you the Endpoints shortly, if you can tell us the name of your OAuth Server/App?");
					} else if (reason === "Unable to register") {
						query_feedback.attr("placeholder", "Error while receiving OTP? Can you please let us know the exact error?");
					}
				});
				span.onclick = function () {
					mo_modal.style.display = "none";
				}
				mo_jwt_skip.onclick = function() {
					mo_modal.style.display = "none";
					jQuery('#mo_jwt_feedback_form_close').submit();
				}
				window.onclick = function (event) {
					if (event.target == mo_modal) {
						mo_modal.style.display = "none";
					}
				}
				return false;
			});
		</script>
		<?php
	}

	/**
	 * Function renders radio boxes.
	 */
	private function render_radios() {
		$deactivate_reasons = array(
			'Does not have the features I am looking for',
			'Confusing Interface',
			'Bugs in the plugin',
			'Unable to register to miniOrange',
			'Other Reasons',
		);
		foreach ( $deactivate_reasons as $deactivate_reason ) {
			?>
			<div style="padding:1px;margin-left:2%;">
				<label style="font-weight:normal;font-size:14.6px" for="<?php echo esc_attr( $deactivate_reason ); ?>">
					<input type="radio" class="mo_jwt_input_radio" style="display: inline-block;" name="mo_jwt_deactivate_reason_radio" value="<?php echo esc_attr( $deactivate_reason ); ?>"
						required>
					<?php echo esc_html( $deactivate_reason ); ?>
				</label>
			</div>
			<?php
		}
	}
}
