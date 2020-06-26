<?php

namespace MeestExpress;

use MeestExpress\Filter;



/**
 *
 * Class MeestExpress Search
 *
 * @package MeestExpress
 *
 */


class Search 
{

	private $core;
	private $request;

	
    public function __construct($meest)
    {
        $this->core = $meest;
    }
	
	
    public function country(Filter $filter = null)
    {
		$replace = array(
			'id' => 'countryID',
			'name' => 'countryDescr'
		);
        return $this->core->request('countrySearch', $filter->getFilters($replace) );
    }
		
    public function region(Filter $filter = null)
    {
		$replace = array(
			'id' => 'regionID',
			'name' => 'regionDescr',
			'katuu' => 'regionKATUU',
			'country_id' => 'countryID',
			'country' => 'countryDescr'
		);
        return $this->core->request('regionSearch', $filter->getFilters($replace) );
    }
	
			
    public function district(Filter $filter = null)
    {
		$replace = array(
			'id' => 'districtID',
			'name' => 'districtDescr',
			'katuu' => 'districtKATUU',
			'region_id' => 'regionID',
			'country' => 'regionDescr'
		);
        return $this->core->request('regionSearch', $filter->getFilters($replace) );
    }
	
			
    public function city(Filter $filter = null)
    {
		$replace = array(
			'id' => 'cityID',
			'name' => 'cityDescr',
			'country_id' => 'countryID',
			'district_id' => 'districtID',
			'district' => 'districtDescr',
			'region_id' => 'regionID',
			'region' => 'regionDescr',
			'region_katuu' => 'regionKATUU'
		);
        return $this->core->request('citySearch', $filter->getFilters($replace) );
    }
			
	
    public function cityByZip($zip)
    {
		$zip = intval($zip);
        return $this->core->request('zipCodeSearch/' . $zip);
    }
	

    public function address(Filter $filter = null)
    {
		$replace = array(
			'city_id' => 'cityID',
			'address' => 'addressDescr'
		);
        return $this->core->request('addressSearch', $filter->getFilters($replace) );
    }			


    public function branchTypes()
    {
        return $this->core->request('branchTypes');
    }			

	
    public function branch(Filter $filter = null)
    {
		$replace = array(
			'num' => 'branchNo',
			'type_id' => 'branchTypeID',
			'name' => 'branchDescr',
			'city_id' => 'cityID',
			'city' => 'cityDescr',
			'district_id' => 'districtID',
			'district' => 'districtDescr',
			'region_id' => 'regionID',
			'region' => 'regionDescr'
		);

        return $this->core->request('branchSearch', $filter->getFilters($replace) );
    }			
	
	
    public function payTerminal($latitude, $longitude)
    {
        return $this->core->request('payTerminalSearch/' . floatval($latitude) . '/' . floatval($longitude) );
    }			


}