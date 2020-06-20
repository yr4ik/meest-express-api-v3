<?php

namespace MeestExpress;

use Exception;


class Result
{
	
	protected $response;
	
	protected $result;
	
	protected $status;
	
	protected $format = 'array';

	public function __construct($response, $format, $throwErrors)
	{
		$this->response = $response;
		$this->format = $format;
		
		switch($this->format)
		{
			case 'array':
				$this->result = is_array($this->response)
					? $this->response
					: json_decode($this->response, 1);
				$this->status = strtolower($this->result['status']);
				
				if($throwErrors && $this->status == 'error')
					 throw new Exception(!empty($this->result['info']['message']) ? $this->result['info']['message'] : 'No error message');
					
				break;
			case 'json':
				$this->result = json_decode($this->response);
				$this->status = strtolower($this->result->status);
				
				if($throwErrors && $this->status == 'error')
					 throw new Exception(!empty($this->result->info->message) ? $this->result->info->message : 'No error message');
					
				break;
			case 'xml':
				$this->result = new \SimpleXMLElement($this->response);
				$this->status = strtolower(strval($this->result->status));
				
				if($throwErrors && $this->status == 'error')
					 throw new Exception(!empty($this->result->info->message) ? strval($this->result->info->message) : 'No error message');
				
				break;
		}
		

		
	}

	

	public function getResponse()
	{
		return $this->result;
	}

	
	public function getResponseSource()
	{
		return $this->response;
	}

	
	public function getStatus()
	{
		return $this->status;
	}
	
	public function getInfo()
	{
		switch($this->format)
		{
			case 'array':
			
				if(isset($this->result['info']))
					return $this->result['info'];
				
				break;
			case 'json':
			case 'xml':
				if(isset($this->result->info))
					return $this->result->info;
				break;
		}
		return false;
	}
	
	public function getResult()
	{
		switch($this->format)
		{
			case 'array':
			
				if(isset($this->result['result']))
					return $this->result['result'];
				
				break;
			case 'json':
			case 'xml':
				if(isset($this->result->result))
					return $this->result->result;
				break;
		}
		return false;
	}


	
}