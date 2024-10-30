<?php
/**
 * Core
 *
 * JWT Client Instance Helper.
 *
 * @category   Common, Core
 * @package    MoOauthClient\Base
 * @author     miniOrange <info@miniorange.com>
 * @license    http://www.gnu.org/copyleft/gpl.html MIT/Expat, see LICENSE.php
 * @link       https://miniorange.com
 */

namespace MoJWT\Accounts;

/**
 * Interface for Accounts Class.
 */
interface AccountsInterface {
	/**
	 * Shows registration page.
	 */
	public function mo_jwt_show_new_registration_page();

	/**
	 * Shows login page.
	 */
	public function verify_password_ui();

}



