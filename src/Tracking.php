<?php

namespace MeestExpress;


/**
 *
 * Class MeestExpress Tracking
 *
 * @package MeestExpress
 *
 */


class Tracking 
{

	private $core;
	private $request;

	
    public function __construct($meest)
    {
        $this->core = $meest;
    }
	
	
	
    public function tracking($trackNumber)
    {
        return $this->core->request('tracking/' . $trackNumber);
    }
	
		
		
		
    public function track($trackNumber)
    {
        return $this->core->request('tracking/' . $trackNumber);
    }		
		
    public function trackInterval($dateFrom, $dateTo, $page=1)
    {
		if(!is_int($dateFrom))
			$dateFrom = strtotime($dateFrom);
		$dateFrom = date('Y-m-d H:i:s', $dateFrom);
		
		if(!is_int($dateTo))
			$dateTo = strtotime($dateTo);
		$dateTo = date('d.m.Y H:i', $dateTo);
		
		$page = intval($page);
		
		
        return $this->core->request("trackingDelivered/{$dateFrom}/{$dateTo}/{$page}");
    }
	
	
    public function trackInDate($searchDate)
    {
		if(!is_int($searchDate))
			$searchDate = strtotime($searchDate);
		$searchDate = date('Y-m-d', $searchDate);

		
        return $this->core->request("trackingDelivered/{$searchDate}");
    }
	
	

	
	

}