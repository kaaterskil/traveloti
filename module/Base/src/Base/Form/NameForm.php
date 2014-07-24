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
 * The form to update a user's name information
 * @author Blair
 */
class NameForm extends Form {
	
	public function __construct() {
		parent::__construct();
		$this->init();
	}
	
	private function init() {
		// Set form id
		$idElement = new Element\Hidden();
		$idElement->setName('user_id');
		$this->add($idElement);
		
		// Set first name element
		$firstNameElement = new Element\Text();
		$firstNameElement->setName('first_name');
		$firstNameElement->setLabel('First Name');
		$firstNameElement->setLabelAttributes(array('class' => ''));
		$firstNameElement->setAttributes(array(
			'class' => 'input-text',
		));
		$this->add($firstNameElement);
		
		// Set middle name element
		$middleNameElement = new Element\Text();
		$middleNameElement->setName('middle_name');
		$middleNameElement->setLabel('Middle Name');
		$middleNameElement->setLabelAttributes(array('class' => ''));
		$middleNameElement->setAttributes(array(
			'class' => 'input-text',
			'placeholder' => 'Optional',
		));
		$this->add($middleNameElement);
		
		// Set last name element
		$lastNameElement = new Element\Text();
		$lastNameElement->setName('last_name');
		$lastNameElement->setLabel('Last Name');
		$lastNameElement->setLabelAttributes(array('class' => ''));
		$lastNameElement->setAttributes(array(
			'class' => 'input-text',
		));
		$this->add($lastNameElement);
		
		// Set display order element
		$displayNameOrderElement = new Element\Select();
		$displayNameOrderElement->setName('display_order');
		$displayNameOrderElement->setLabel('Display Name Order');
		$displayNameOrderElement->setLabelAttributes(array('class' => ''));
		$displayNameOrderElement->setValueOptions(array(
			'fnln' => 'First Name Last Name',
			'lnfn' => 'Last Name First Name',
		));
		$displayNameOrderElement->setAttributes(array(
			'onselect' => 'showDispalyName(this)',
		));
		$this->add($displayNameOrderElement);
		
		// Set alternate name element
		$alternateNameElement = new Element\Text();
		$alternateNameElement->setName('alternate_name');
		$alternateNameElement->setLabel('Alternate Name');
		$alternateNameElement->setLabelAttributes(array('class' => ''));
		$alternateNameElement->setAttributes(array(
			'class' => 'input-text',
			'placeholder' => 'Optional',
		));
		$this->add($alternateNameElement);
		
		// Set password element
		$passwordElement = new Element\Password();
		$passwordElement->setName('password');
		$passwordElement->setLabel('Password');
		$passwordElement->setLabelAttributes(array('class' => ''));
		$passwordElement->setAttributes(array(
			'class' => 'input-text',
		));
		$this->add($passwordElement);

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