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
use Location\Model\Location;
use Location\Form\LocationForm;

/**
 * LocationController Class
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @subpackage	Controller
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */
class LocationController extends AbstractActionController {
	
	protected $locationTable;
	
	public function addAction() {
		$form = new LocationForm();
		$form->get('submit')->setValue('Add');
		
		$request = $this->getRequest();
		if($request->isPost()) {
			$location = new Location();
			$form->setInputFilter($location->getInputFilter());
			$form->setData($request->getPost());
			
			if($form->isValid()) {
				$location->exchangeArray($form->getData());
				$this->getLocationTable()->saveLocation($location);
				
				// Redirect to list of locations
				return $this->redirect()->toRoute('location');
			}
		}
		return array('form' => $form);
	}
	
	public function deleteAction() {
		$id = (int) $this->params()->fromRoute('location_id', 0);
		if(!$id) {
			return $this->redirect()->toRoute('location');
		}
		
		$request = $this->getRequest();
		if($request->isPost()) {
			$delete = $request->getPost('del', 'No');
			
			if($delete == 'Yes') {
				$id = (int) $request->getPost('location_id');
				$this->getLocationTable()->deleteLocation($id);
			}
			
			// Redirect to list of locations
			return $this->redirect()->toRoute('location');
		}
		
		return array(
			'location_id' => $id,
			'location' => $this->getLocationTable()->getLocation($id),
		);
	}
	
	public function editAction() {
		$id = (int) $this->params()->fromRoute('location_id', 0);
		if(!$id) {
			return $this->redirect()->toRoute('location', array(
				'action' => 'add'
			));
		}
		$location = $this->getLocationTable()->getLocation($id);
		
		$form = new LocationForm();
		$form->bind($location);
		$form->get('submit')->setAttribute('value', 'Edit');
		
		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setInputFilter($location->getInputFilter());
			$form->setData($request->getPost());
			
			if($form->isValid()) {
				$this->getLocationTable()->saveLocation($form->getData());
				
				// Redirect to list of locations
				return $this->redirect()->toRoute('location');
			}
		}
		
		return array(
			'location_id' => $id,
			'form' => $form,
		);
	}
	
	public function indexAction() {
		return new ViewModel(array(
			'locations' => $this->getLocationTable()->fetchAll(),
		));
	}
	
	public function getLocationTable() {
		if(!$this->locationTable) {
			$sm = $this->getServiceLocator();
			$this->locationTable = $sm->get('Location\Model\LocationTable');
		}
		return $this->locationTable;
	}
}
?>