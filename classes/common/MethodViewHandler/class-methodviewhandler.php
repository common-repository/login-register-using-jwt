<?php
/**
 * Core
 *
 * JWT Method view Handler.
 *
 * @category   Common, Core
 * @package    MoJWT\MethodViewHandler
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT;

use MoJWT\Base\InstanceHelper;

/**
 * Class to Method View Handler.
 *
 * @category Common, Core
 * @package  MoJWT\MethodViewHandler
 * @author   miniOrange <info@miniorange.com>
 * @license  http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link     https://miniorange.com
 */
class MethodViewHandler {

	/**
	 * Instance Helper
	 *
	 * @var \MoJWT\Base\InstanceHelper $instance_helper
	 * */
	private $instance_helper;

	/**
	 * All Method Instance
	 *
	 * @var array $all_method_instances
	 **/
	private $all_method_instances;

	/**
	 * Selected method extra config
	 *
	 * @var \MoJWT\Base\InstanceHelper $selected_method_extra_config
	 * */
	private $selected_method_extra_config;

	/**
	 * Selected instance with method config
	 *
	 * @var \MoJWT\Base\InstanceHelper $selected_method_extra_config
	 * */
	private $get_method_ins;

	/**
	 * Selected method config
	 *
	 * @var \MoJWT\Base\InstanceHelper $selected_method_extra_config
	 * */
	private $selected_method;

	/**
	 * Selected method extra config
	 *
	 * @var \MoJWT\Base\InstanceHelper $selected_method_extra_config
	 * */
	private $method_extra_config;

	/**
	 * Disable UI based on user license expiry status
	 *
	 * @var $ui_status
	 * */
	private $ui_status;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $mj_util, $mo_jwt_license_subscription_namespace;
		$this->instance_helper      = new InstanceHelper();
		$this->get_method_ins       = $this->instance_helper->get_all_method_instances();
		$this->all_method_instances = $this->sort_instances( $this->instance_helper->get_registered_method_views() );
		$this->selected_method      = $mj_util->mo_jwt_get_transient( 'mo_jwt' );
		$this->method_extra_config  = $mj_util->mo_jwt_get_option( 'mo_jwt_config_settings' );
		$this->ui_status            = $mj_util->get_versi() && null !== $mo_jwt_license_subscription_namespace ? $mo_jwt_license_subscription_namespace::get_html_disabled_status() : '';

	}

	/**
	 * Load All Methods
	 */
	private function load_all_methods_view() {
		$all_method_slug = $this->get_all_method_slug();
		$count           = count( $this->all_method_instances );
		$all_method_slug = implode( ',', $all_method_slug );
		for ( $i = 1; $i <= $count; $i++ ) {
			$instance = new $this->all_method_instances[ $i ]();
			$this->single_method_block_ui( $instance->get_method_name(), $instance->get_method_slug(), $instance->get_image_name(), $all_method_slug );
		}
	}

	/**
	 * Sort the Instances according to priority
	 *
	 * @param mixed $all_method_instances Method of all instance.
	 *
	 * @return array
	 */
	private function sort_instances( $all_method_instances ) {
		$sort_instance = array();
		foreach ( $all_method_instances as $key => $value ) {
			$instance                                    = new $value();
			$sort_instance [ $instance->get_priority() ] = $value;
		}
		return $sort_instance;
	}

	/**
	 * Get All Methods Slug
	 */
	private function get_all_method_slug() {
		$methods_slug = array();
		foreach ( $this->all_method_instances as $key => $value ) {
			$instance = new $value();
			array_push( $methods_slug, $instance->get_method_slug() );
		}
		return $methods_slug;
	}

	/**
	 * Load All Method's Configuration view
	 */
	private function load_methods_config_view() {
		foreach ( $this->all_method_instances as $key => $value ) {
			$instance = new $value( false, $this->selected_method );
			if ( method_exists( $instance, 'load_config_view' ) ) {
				$instance->load_config_view();
			}
		}
	}

	/**
	 * Load Single Method Block
	 *
	 * @param mixed $method_name Name of method.
	 * @param mixed $method_slug Slug of method.
	 * @param mixed $image_name Image of method.
	 * @param mixed $all_method_slug Slugs of all methods.
	 *
	 * @return void
	 */
	private function single_method_block_ui( $method_name, $method_slug, $image_name, $all_method_slug ) {      ?>
			<div class="mo_jwt_method">
				<label>
					<input type="radio" name="mo_jwt_method" id="<?php echo esc_attr( $method_slug ); ?>" value="<?php echo esc_attr( $method_slug ); ?>" 
					<?php
					if ( $this->selected_method === $method_slug ) {
						echo 'checked';}
					?>
					onclick="MoJWTdivVisibility('<?php echo esc_attr( $method_slug ); ?>', '<?php echo esc_attr( $all_method_slug ); ?>')">
					<img src="<?php echo esc_url( MJ_URL . '/resources/images/icons/' . $image_name ); ?>"><br/><?php echo esc_html( $method_name ); ?>
					<br>
				</label>
			</div>
		<?php
	}

	/**
	 * Load Single Method Doc View
	 */
	private function load_configure_method_doc_view() {
		foreach ( $this->all_method_instances as $key => $value ) {
			$instance = new $value();
			if ( $this->selected_method === $instance->get_method_slug() && method_exists( $instance, 'load_doc_view' ) ) {
				$instance->load_doc_view();
			}
		}
	}

	/**
	 * Render UI of Config Tab
	 */
	public function render_ui() {
		global $mj_util;
		?>
			<div class="mo_jwt_support_layout">
				<div class="mo_jwt_settings_table">
					<h1><b>JWT Configuration</b> </h1>
					<br>
					<form action="" id="mo-jwt-method-form" method="POST">
						<?php wp_nonce_field( 'mo_jwt_config_settings', 'mo_jwt_config_nonce' ); ?>
						<input type="hidden" name="option" value="mo_jwt_config_settings">
						<div id="jwt-method" >
							<?php $this->load_all_methods_view(); ?>
						</div>
						<br>
						<hr width="86%">
						<div class="mo-jwt-method-config" id="jwt-method-config">
								<?php $this->load_methods_config_view(); ?>
						</div>
						<div class="mo-jwt-save-config-button">
							<input type="hidden" name="action" id="mo_jwt_save_config_input" value="Save Configuration">
							<button type="submit" style="margin:10px; width:120px;" id="mo_jwt_save_config_button" onclick="" class="button button-primary button-large" <?php echo esc_attr( $this->ui_status ); ?>>Save Settings</button>
						</div>
					</form>

				</div>
			</div>
			<br>

			<!-- Doc view  -->
			<div>
				<?php $this->load_configure_method_doc_view(); ?>
			</div>

			<script>
				function MoJWTMethodSave(action){
					document.getElementById("mo_jwt_save_config_input").value = action;
					div_list = ['jwtcreate', 'jwtdelete','jwtlogin','jwtregister'];
					div_list2 = ['OAuth', 'SHA', 'JWKS'];
					div_list.forEach( (item, index) => {
						if( document.getElementById(item).checked ) {
							set_required(item);
							if( 'jwtlogin' ==  item) {
								div_list2.forEach( (item2, index2) => {
									if( document.getElementById(item2).checked ) {
										set_required(item2);
									}
								} );
							}
						} else {
							un_set_required( item );
							if( 'jwtlogin' ==  item) {
								div_list2.forEach( (item2, index2) => {
									if( document.getElementById(item2).checked ) {
										un_set_required(item2);
									}
								} );
							}
						}
					} );
					// var flag = formcheck();
					// if( flag == 0 ) {
						// document.getElementById("mo-jwt-method-form").submit();
					// }
				}

				function set_required( item ) {
					require_fields = document.getElementsByClassName( item + "_required" );
					for( i=0;i<require_fields.length;i++) {
						require_fields[i].setAttribute('id', item + "testing" + i);
						document.getElementById(item + "testing" + i).required = true;
						// require_fields[i].innerHTML='test';
					}
				}

				function un_set_required( item ) {
					require_fields = document.getElementsByClassName( item + "_required" );
					for( i=0;i<require_fields.length;i++) {
						require_fields[i].setAttribute('id', item + "testing" + i);
						document.getElementById(item + "testing" + i).required = true;
						// require_fields[i].innerHTML='test';
					}
				}
			</script>

		<?php
	}

}
