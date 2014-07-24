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
 * LocationTable Class
 *
 * @category	Traveloti
 * @package		Traveloti_Location
 * @subpackage	Model
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */
class LocationTable {
	
	protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Modified by Blair to return only the first 200 records
     *
     * @return ResultSet
     */
    public function fetchAll() {
        $resultSet = $this->tableGateway->select(function (Select $select) {
        	$select->where('city > ""');
        	$select->order('city ASC')->limit(300);
        });
        return $resultSet;
    }

    public function getLocation($id) {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('location_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveLocation(Location $location) {
        $data = array(
            'area_code' 	=> $location->getAreaCode(),
        	'city'			=> $location->getCity(),
        	'country_id'	=> $location->getCountry(),
        	'latitude'		=> $location->getLatitude(),
        	'longitude'		=> $location->getLongitude(),
        	'metro_code'	=> $location->getMetroCode(),
        	'postal_code'	=> $location->getPostalCode(),
        	'region'		=> $location->getRegion(),
        );

        $id = (int) $location->getId();
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getLocation($id)) {
                $this->tableGateway->update($data, array('location_id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteLocation($id) {
        $this->tableGateway->delete(array('location_id' => $id));
    }
}
?>