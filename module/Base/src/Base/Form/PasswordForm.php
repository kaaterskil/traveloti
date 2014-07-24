<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * The form to update a user's password
 * @author Blair
 */
class PasswordForm extends Form {
	
	public function __construct() {
		parent::__construct();
		$this->init();
	}
	
	private function init() {
		// Set form id
		$idElement = new Element\Hidden();
		$idElement->setName('user_id');
		$this->add($idElement);
		
		// Set current password element
		$currentPasswordElement = new Element\Password();
		$currentPasswordElement->setName('current_password');
		$currentPasswordElement->setLabel('Current');
		$currentPasswordElement->setLabelAttributes(array('class' => ''));
		$currentPasswordElement->setAttributes(array(
			'class' => 'input-text',
		));
		$this->add($currentPasswordElement);
		
		// Set new password element
		$newPasswordElement = new Element\Password();
		$newPasswordElement->setName('new_password');
		$newPasswordElement->setLabel('New');
		$newPasswordElement->setLabelAttributes(array('class' => ''));
		$newPasswordElement->setAttributes(array(
			'class' => 'input-text',
		));
		$this->add($newPasswordElement);
		
		// Set new password element
		$verifyPasswordElement = new Element\Password();
		$verifyPasswordElement->setName('password_verify');
		$verifyPasswordElement->setLabel('Re-type New');
		$verifyPasswordElement->setLabelAttributes(array('class' => ''));
		$verifyPasswordElement->setAttributes(array(
			'class' => 'input-text',
		));
		$this->add($verifyPasswordElement);

		// Set submit element
		$submitElement = new Element\Submit();
		$submitElement->setName('btnSubmit');
		$submitElement->setAttribute('tabindex', -1);
		$this->add($submitElement);
		
		$cancelElement = new Element\Button();
		$cancelElement->setName('btnCancel');
		$cancelElement->setValue('Cancel');
		$cancelElement->setAttributes(array(
			'class' => '',
		));
		$this->add($cancelElement);
	}
}
?>