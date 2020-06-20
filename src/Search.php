<?php

namespace MeestExpress;

use MeestExpress\Filter;


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
	



}