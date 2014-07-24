<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\View\Helper;

use ScnSocialAuth\View\Helper\SocialSignInButton as ScnButton;

/**
 * SocialSignInButton Class
 *
 * @author Blair
 */
class SocialSignInButton extends ScnButton {
	
	public function __invoke($provider, $redirect = false) {
		$redirectArg = $redirect ? '?redirect=' . $redirect : '';
		
		echo '<a class="" href="'
				. $this->view->url('scn-social-auth-user/login/provider', array('provider' => $provider))
				. $redirectArg . '">' . "\n"
				. '<i class=""></i>' . "\n"
				. '</a>' . "\n";
	}
}
?>