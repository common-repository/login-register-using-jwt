<?php
/**
 * Plugin
 *
 * JWT Login Licensing Page.
 *
 * @category   Core
 * @package    MoJWT
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT;

/**
 * Class to Handle Licensing page UI and functions.
 *
 * @category Core
 * @package  MoJWT
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class Licensing {
	/**
	 * Additional css
	 *
	 * @return void
	 */
	public static function emit_css() {
		?>
		<style>
			.mo_jwt_login_licensing{
				padding:5px 15px 0px 15px;
				background-color: #FFFFFF;
				border: 1px solid #CCCCCC;
				border-radius: 0.3em;
				margin:50px 0px 0px 0px;
			}

			.mo_jwt_login_licensing_heading{

			text-align:center;
			word-spacing: 4px;
			font-size: 25px;
			font-weight:600;
			letter-spacing: 1px;
			text-transform: uppercase;
			margin-top:15px;
			margin-bottom:25px;

			}
			.mo_jwt_login_licensing_container {
				margin-left: 2px ;
				padding: 10px;
			}

			.mo_jwt_login_license_button{
				width:50%;
				background-color: #2271b1;
				height: 40px;
				border-radius: 5px;
				color: white;
				font-size: 20px;
				cursor: pointer;
			}
			.moct-align-left {
				text-align: left;
			}
			.moct-align-right {
				text-align: right;
			}
			.moct-align-center {
				text-align: center;
			}
			.moc-licensing-notice {
				width: 90%;
				margin-top: 5%;
			}
			.mo_jwt_login_licensing_plan_header {
				font-size: 32px;
				font-variant: small-caps;
				border-radius: 1rem 1rem 0px 0px;
			}
			.mo_jwt_login_licensing_plan_header hr {
				margin: 1.5rem 0;
			}
			.mo_jwt_login_licensing_plan_feature_list {
				font-size: 12px;
				padding-top: 10px;
			}
			.mo_jwt_login_licensing_plan_feature_list li {
				text-align: left;
				padding: 10px;
				border: none;
			}
			.mo_jwt_login_licensing_plan_feature_list li:nth-child(even) {
				background-color: #e4f0f0;
			}
			.mo_jwt_login_licensing_plan_feature_list li:nth-child(odd) {
				background-color: #f5fafa;
			}
			.mo_jwt_login_licensing_plan_usp {
				font-size: 18px;
				font-weight: 500;
				padding-bottom: 10px;
			}
			.mo_jwt_login_licensing_plan_price {
				font-size: 24px;
				font-weight: 400;
			}
			.mo_jwt_login_licensing_plan_name {
				font-size: 32px;
				font-weight: 500;
			}
			.mo_jwt_login_licensing_plan {
				border-radius: 0.3rem;
				border: 1px solid #00788E;
				margin: 0.5rem 0;
				box-shadow: 0 4px 8px 0 rgba(0,0,0,0.4);
				transition: 0.3s;
			}
			.mo_jwt_login_licensing_plan:hover {
				margin-top: -.25rem;
				margin-bottom: .25rem;
				/* border: 1px solid #17a2b8; */
				border: 1px solid rgb(112, 165, 245);
				box-shadow: 0 16px 32px 0 rgba(112, 165, 245, 0.8);
			}
			.moc-lp-buy-btn {
				border-radius: 5rem;
				letter-spacing: .1rem;
				font-weight: bold;
				padding: 1rem;
				opacity: 0.7;
			}
			.moc-lp-buy-btn:hover {
				opacity: 1;
			}
			.moc-lp-highlight {
				box-shadow: 0 16px 32px 0 #563d7c66;
				border: 1px solid #2B1251;
			}
			.moc-lp-highlight:hover {
				border: 1px solid #563d7c;
				box-shadow: 0 16px 32px 0 #563d7ccc;
			}
			.btn-purple {
				color: #ffffff;
				background: radial-gradient(circle, #563d7c, #452c6b);
				border-color: #563d7c;
			}
			.btn-purple:hover {
				background: radial-gradient(circle, #452c6b, #563d7c);
			}
			.mo_jwt_login_licensing_plan_select{
				margin-top: 10px;
				margin-bottom: 20px;
				width: 100%;
				height: 40px;
				font-size: 16px !important;
				border-radius: 5px !important;
				text-align: center;
				text-align-last: center;
			}
			.mo_jwt_login_licensing_plan_select option{
				text-align: center;
			}
			.mo_jwt_mailto{
				color: white;
			}
		</style>
		<?php
	}

	/**
	 * Displays Licensing page
	 *
	 * @return void
	 */
	public static function show_licensing_page() {
			self::emit_css();
			global $mj_util;
		?>
	<input type="hidden" value="<?php echo esc_attr( $mj_util->mo_jwt_is_customer_registered() ); ?>" id="mo_jwt_customer_registered">
	<div class="mo_jwt_support_layout">
		<form style="display:none;" id="mo_jwt_loginform"
			action="<?php echo esc_url( get_option( 'mo_jwt_host_name' ) . '/moas/login' ); ?>"
			target="_blank" method="post">
			<input type="email" name="username" value="<?php echo esc_attr( get_option( 'mo_jwt_admin_email' ) ); ?>"/>
			<input type="text" name="redirectUrl"
			value="<?php echo esc_url( get_option( 'mo_jwt_host_name' ) . '/moas/initializepayment' ); ?>"/>
			<input type="text" name="requestOrigin" id="mo_jwt_request_origin"/>
		</form>
		<form style="display:none;" id="mo_jwt_viewlicensekeys"
		action="<?php echo esc_url( get_option( 'mo_jwt_host_name' ) . '/moas/login' ); ?>"
			target="_blank" method="post">
			<input type="email" name="username" value="<?php echo esc_attr( get_option( 'mo_jwt_admin_email' ) ); ?>"/>
			<input type="text" name="redirectUrl"
			value="<?php echo esc_url( get_option( 'mo_jwt_host_name' ) . '/moas/mo_jwt_viewlicensekeys' ); ?>"/>
		</form>
		<!-- End Important JSForms -->
		<!-- Licensing Table -->
		<div class="mo_jwt_login_licensing_container" style="background-color: white;">
			<div class="mo_jwt_login_licensing_header">
				<div class="container-fluid">
					<div class="row">
						<div class="col-6 moct-align-right">
							&nbsp;
						</div>
						<div class="col-6 moct-align-right">
							&nbsp;
						</div>
					</div>
					<div class="row justify-content-center mx-15">

						<!-- Licensing Plans -->
						<!-- free Plan -->
						<div class="col-6 moct-align-center">
						<div class="mo_jwt_login_licensing_plan card-body">
								<!-- Plan Header -->
								<div class="mo_jwt_login_licensing_plan_header">
									<div class="mo_jwt_login_licensing_plan_name">Free</div>
									<div class="mo_jwt_login_licensing_plan_price"><sup>$</sup>0<sup>*</sup></div>
								</div>
								<br>
								<button class="mo_jwt_login_license_button" style="cursor: default;">
								<?php
								if ( $mj_util->check_versi( 1 ) ) {
									echo 'FREE PLAN';
								} else {
									echo 'Current Plan';}
								?>
								</button>

								<!-- Plan Header End -->
								<!-- Plan Feature List -->
								<div class="mo_jwt_login_licensing_plan_feature_list">
									<ul>
									<li><b>Create JWT with</b> - <br><br>&#9989;&emsp;HS256<br>&#10060;&emsp;RS256<br>&#10060;&emsp;Custom Signing Key<br>&#10060;&emsp;Custom Decryption Key<br>&#10060;&emsp;Custom JWT expiration</li>
									<!-- <li>&#9989;&emsp;Fetch </li> -->
									<li><b>Register user for JWT</b> - <br><br>&#9989;&emsp;Endpoint to create user<br>&#10060;&emsp;Choose Default Role<br>&#10060;&emsp;Send role parameter in endpoint request<br>&#10060;&emsp;Extra Security Key<br></li>
									<li><b>Delete user with JWT</b> - <br><br>&#9989;&emsp;Endpoint to delete user<br>&#10060;&emsp;Extra Security Key<br></li>
									<li><b>Login/SSO user with JWT</b> - <br><br>&#9989;&emsp;Accept JWT from URL parameter <br>&#10060;&emsp;Accept JWT from Cookie<br>&#10060;&emsp;Accept JWT from Request Header<br>&#10060;&emsp;Custom token parameter name<br>&#9989;&emsp;Auto-redirection on login to home URL or same page<br>&#9989;&emsp;Token Validation using HS256<br>&#10060;&emsp;Token Validation using RS256<br>&#10060;&emsp;Custom decryption key<br>&#10060;&emsp;Token validation from JWKS<br>&#10060;&emsp;Token validation from OAuth/OIDC provider<br>&#10060;&emsp;User basic profile mapping<br>&#10060;&emsp;User Role mapping<br>&#10060;&emsp;Audit login users</li>
									</ul>
								</div>
								<!-- Plan Feature List End -->
							</div>
						</div>
						<!-- Standard Plan End -->
						<!-- Premium Plan -->

						<div class="col-6 moct-align-center">
							<div class="mo_jwt_login_licensing_plan card-body">
								<!-- Plan Header -->
								<div class="mo_jwt_login_licensing_plan_header">
									<div class="mo_jwt_login_licensing_plan_name">Premium</div>
									<div class="mo_jwt_login_licensing_plan_price"><sup>$</sup>449<sup>*</sup></div>
								</div>
								<br>

								<a class="mo_jwt_mailto" ><button class="mo_jwt_login_license_button" onclick="mo_jwt_upgradeform('wp_rest_jwt_login_premium_plan')">Buy now</button></a>

								<!-- Plan Header End -->
								<!-- Plan Feature List -->
								<div class="mo_jwt_login_licensing_plan_feature_list">
									<ul>
										<li><b>Create JWT with</b> - <br><br>&#9989;&emsp;HS256<br>&#9989;&emsp;RS256<br>&#9989;&emsp;Custom Signing Key<br>&#9989;&emsp;Custom Decryption Key<br>&#9989;&emsp;Custom JWT expiration</li>
										<!-- <li>&#9989;&emsp;Fetch </li> -->
										<li><b>Register user for JWT</b> - <br><br>&#9989;&emsp;Endpoint to create user<br>&#9989;&emsp;Choose Default Role<br>&#9989;&emsp;Send role parameter in endpoint request<br>&#9989;&emsp;Extra Security Key<br></li>
										<li><b>Delete user with JWT</b> - <br><br>&#9989;&emsp;Endpoint to delete user<br>&#9989;&emsp;Extra Security Key<br></li>
										<li><b>Login/SSO user with JWT</b> - <br><br>&#9989;&emsp;Accept JWT from URL parameter <br>&#9989;&emsp;Accept JWT from Cookie<br>&#9989;&emsp;Accept JWT from Request header <br>&#9989;&emsp;Custom token parameter name <br>&#9989;&emsp;Auto-redirection on login to home URL or same page<br>&#9989;&emsp;Token Validation using HS256<br>&#9989;&emsp;Token Validation using RS256<br>&#9989;&emsp;Custom decryption key<br>&#9989;&emsp;Token validation from JWKS<br>&#9989;&emsp;Token validation from OAuth/OIDC provider<br>&#9989;&emsp;User basic profile mapping<br>&#9989;&emsp;User Role Mapping<br>&#9989;&emsp;Audit login users</li>

									</ul>
								</div>
								<!-- Plan Feature List End -->
							</div>
						</div>
						<!-- Premium Plan End -->
						<!-- Enterprise Plan -->

					</div>
					</div>


						<!-- Enterprise Plan End -->
						<!-- Licensing Plans End -->
						<div class=mo_jwt_login_licensing>
						<!-- <div class="moc-licensing-notice"> -->
							<h6 class="mo_jwt_login_licensing_heading">LICENSING POLICY</h6>
							<span style="color: red;">*</span>We provide deep discounts on bulk license purchases and pre-production environment licenses. As the no. of licenses increases, the discount percentage also increases. Contact us at <a href="mailto:apisupport@xecurify.com?subject=JWT SSO Plugin - Enquiry" target="_blank">apisupport@xecurify.com</a> for more information.
							<br>
							<br>
							<strong>Note:</strong> All the data remains within your premises/server. We do not provide the developer license for our paid plugins and the source code is protected. It is strictly prohibited to make any changes in the code without having written permission from miniOrange. There are hooks provided in the plugin which can be used by the developers to extend the plugin's functionality.
							<br>
							<br>
							At miniOrange, we want to ensure you are 100% happy with your purchase. If the premium plugin you purchased is not working as advertised and you've attempted to resolve any issues with our support team, which couldn't get resolved. Please email us at <a href="mailto:apisupport@xecurify.com?subject=JWT SSO Plugin - Enquiry" target="_blank">apisupport@xecurify.com</a> for any queries regarding the return policy.
							<br>
							<br>
							<br>
						<!-- </div> -->
					</div>
		<!-- End Licensing Table -->
		<a  id="mobacktoaccountsetup" style="display:none;" href="<?php echo ! empty( $_SERVER['REQUEST_URI'] ) ? esc_attr( add_query_arg( array( 'tab' => 'account' ), sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) ) : ''; ?>">Back</a>
		<!-- JSForms Controllers -->
		<script>

			function mo_jwt_upgradeform(planType) {
				if(planType === "") {
					location.href = "https://wordpress.org/plugins/wp-rest-api-authentication/";
					return;
				} else {
					url = "https://portal.miniorange.com/initializepayment?requestOrigin=" + planType;
					window.open(url, '_blank');
				}

			}

			function getlicensekeys() {
				// if(jQuery('#mo_jwt_customer_registered').val()==1)
				jQuery('#mo_jwt_viewlicensekeys').submit();
			}
		</script>
		</div>
	</div>
		<?php

	}
}
