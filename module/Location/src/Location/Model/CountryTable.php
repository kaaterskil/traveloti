<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @subpackage	Model
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Location\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
 * CountryTable Class
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @subpackage	Model
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */
class CountryTable {
	
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll() {
		$resultSet = $this->tableGateway->select(function(Select $select) {
			$select->order('country ASC');
		});
		return $resultSet;
	}
	
	public function getCountry($id) {
		$is = (int) $id;
		$rowset = $this->tableGateway->select(array('country_id' => $id));
		$row = $rowset->current();
		if(!$row) {
			throw new \Exception('Could not find row' . $id);
		}
		return $row;
	}
	
	public function saveCountry(Country $country) {
		$data = array(
			'iso_code' => $country->getIsoCode(),
			'name' => $country->getName(),
		);
		
		$id = (int) $country->getId();
		if($id == 0) {
			$this->tableGateway->insert($data);
		} else {
			if($this->getCountry($id)) {
				$this->tableGateway->update($data, array('country_id' => $id));
			} else {
				throw new \Exception('Form does not exist');
			}
		}
	}
	
	public function deleteCountry($id) {
		$this->tableGateway->delete(array('country_id' => $id));
	}
}
?>