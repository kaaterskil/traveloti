<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @subpackage	Controller
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Location\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Location\Model\Country;
use Location\Form\CountryForm;

/**
 * CountryController Class
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @subpackage	Controller
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */
class CountryController extends AbstractActionController {
	
	protected $countryTable;
	
	public function addAction() {
		$form = new CountryForm();
		$form->get('submit')->setValue('Add');
		
		$request = $this->getRequest();
		if($request->isPost()) {
			$country = new Country();
			$form->setInputFilter($country->getInputFilter());
			$form->setData($request->getPost());
			if($form->isValid()) {
				$country->exchangeArray($form->getData());
				$this->getCountryTable()->saveCountry($country);
				
				// Redirect to list of countries
				return $this->redirect()->toRoute('country');
			}
		}
		
		return array('form' => $form);
	}
	
	public function deleteAction() {
		$id = (int) $this->params()->fromRoute('country_id', 0);
		if(!$id) {
			return $this->redirect()->toRoute('country');
		}
		
		$request = $this->getRequest();
		if($request->isPost()) {
			$isDelete = $request->getPost('del', 'No');
			if($isDelete == 'Yes'){
				$id = (int) $request->getPost('country_id');
				$this->getCountryTable()->deleteCountry($id);
			}
			
			// Redirect to list of countries
			return $this->redirect()->toRoute('country');
		}
		
		return array(
			'country_id' => $id,
			'country' => $this->getCountryTable()->getCountry($id),
		);
	}
	
	public function editAction() {
		$id = (int) $this->params()->fromRoute('country_id', 0);
		if(!$id){
			return $this->redirect()->toRoute('country', array('action'=> 'add'));
		}
		$country = $this->getCountryTable()->getCountry($id);
		
		$form = new CountryForm();
		$form->bind($country);
		$form->get('submit')->setAttribute('value', 'Edit');
		
		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setInputFilter($country->getInputFilter());
			$form->setData($request->getPost());
			if($form->isValid()){
				$this->getCountryTable()->saveCountry($form->getData());
				
				// Redirect to list of countries
				return $this->redirect()->toRoute('country');
			}
		}
		
		return array(
			'id' => $id,
			'form' => $form,
		);
	}
	
	public function indexAction() {
		return new ViewModel(array(
			'countries' => $this->getCountryTable()->fetchAll(),
		));
	}
	
	public function getCountryTable() {
		if(!$this->countryTable) {
			$sm = $this->getServiceLocator();
			$this->countryTable = $sm->get('Location\Model\CountryTable');
		}
		return $this->countryTable;
	}
}
?>