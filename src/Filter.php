<?php

namespace MeestExpress;

class Filter
{
	protected $data = [];

	public function __construct(array $data = [])
	{
		$this->addData($data);
	}

	public function addData(array $data): Filter
	{
		$this->data = array_merge($this->data, $data);
		return $this;
	}

	public function setData(string $key, string $value): Filter
	{
		$this->data[$key] = $value;
		return $this;
	}

	public function getFilters(array $replaces = []): array
	{
		$data = $this->data;
		foreach($replaces as $search => $replace)
		{
			if(isset($data[$search]))
			{
				$data[$replace] = $data[$search];
				unset($data[$search]);
			}
		}
		return array('filters' => $data);
	}
	
	public function getData($replaces): array
	{
		return $this->data;
	}

	public function __set($name, $value): Filter
	{
		$this->data[$name] = $value;
		return $this;
	}

	public function __get($name)
	{
		return $this->data[$name];
	}
}