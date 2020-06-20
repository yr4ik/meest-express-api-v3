<?php

namespace MeestExpress;

use Exception;
use MeestExpress\Search;
use MeestExpress\Result;


/**
 *
 * Class MeestExpress
 *
 * @package MeestExpress
 *
 */

class MeestExpress
{
    /**
     * Login for API MeestExpress.
     *
     * @var string
     */
    protected $login;
    /**
     * Pass for API MeestExpress.
     *
     * @var string
     */
    protected $password;
	
    /**
     * Base URL for API MeestExpress.
     *
     * @var string
     */
    protected $apiUrl = 'https://api.meest.com/v3.0/openAPI';

    /**
     * @var bool Throw exceptions when in response is error
     */
    protected $throwErrors = false;

    /**
     * @var string Format of returned data - array, json, xml
     */
    protected $format = 'array';

    /**
     * @var string Connection type (curl | file_get_contents)
     */
    protected $connectionType = 'curl';

    /**
     * COOKIE var name for storage access token
     *
     * @var string
     */
    protected $cookieTokenVar = '_MeestExpressApiToken';

    /**
     * @var string token of response
     */
    protected $token = '';


    /**
     * Default constructor.
     *
     * @param string $login           MeestExpress API Login
     * @param bool   $throwErrors    Throw request errors as Exceptions
     * @param bool   $connectionType Connection type (curl | file_get_contents)
     *
     * @return MeestExpress
     */
    public function __construct($login, $pasword, $throwErrors = true, $connectionType = 'curl')
    {
		$this->token = $this->getToken();
        $this->throwErrors = $throwErrors;
		
        return $this
            ->setLogin($login)
            ->setPassword($pasword)
            ->setConnectionType($connectionType)
			->authorize();
    }

    /**
     * Setter for login property.
     *
     * @param string $login MeestExpress API login
     *
     * @return MeestExpress
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * Getter for login property.
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Setter for pass property.
     *
     * @param string $password MeestExpress API password
     *
     * @return MeestExpress
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Getter for password property.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
	
	
    /**
     * Setter varible name for storage cookie property.
     *
     * @param string $password MeestExpress API password
     *
     * @return MeestExpress
     */
    public function setCookieStorageVar($var)
    {
        $this->cookieTokenVar = $var;
        return $this;
    }

    /**
     * Getter varible name for storage cookie property.
     *
     * @return string
     */
    public function getCookieStorageVar()
    {
        return $this->cookieTokenVar;
    }

    /**
     * Setter for $connectionType property.
     *
     * @param string $connectionType Connection type (curl | file_get_contents)
     *
     * @return $this
     */
    public function setConnectionType($connectionType)
    {
        $this->connectionType = $connectionType;
        return $this;
    }

    /**
     * Getter for $connectionType property.
     *
     * @return string
     */
    public function getConnectionType()
    {
        return $this->connectionType;
    }


    /**
     * Setter for format property.
     *
     * @param string $format Format of returned data by methods (json, xml, array)
     *
     * @return MeestExpress
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Getter for format property.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

	

    public function setToken($token, $expire=0, $refresh='')
    {
		$this->token = $token;
		
		if($expire > 0)
		{
			$expire = time() + $expire;
			setcookie($this->getCookieStorageVar(), implode(':', array($this->token, $expire, $refresh)), $expire, '/');
		}
		
        return $this;
    }
	
    public function getToken()
    {
		if(!empty($this->token))
			return $this->token;
		
		$cokie_var = $this->getCookieStorageVar();
		if(!empty($_COOKIE[$cokie_var]))
		{
			list($token, $expire, $refresh) = explode(':', $_COOKIE[$cokie_var], 3);
			
			// Update token if expire
			if($expire - time() < (10 * 60))
			{
				$data = array(
					'refreshToken' => $refresh
				);
				$result = $this->request('refreshToken', $data, 'array')->getResult();
				
				if($result)
				{
					$token = $result['token'];
					$this->setToken($result["token"], $result["expiresIn"], $result["refreshToken"]);
				}
			}
			
			return $token;
		}
        return false;
    }

    public function authorize()
    {
		$token = $this->getToken();

		if(empty($token))
		{
			$data = array(
				'username' => $this->getLogin(),
				'password' => $this->getPassword()
			);
			$result = $this->request('auth', $data, 'array')->getResult();

			if($result)
				$this->setToken($result["token"], $result["expiresIn"], $result["refreshToken"]);
		}
        return $this;
    }

	

    /**
     * Converts array to xml.
     *
     * @param array
     */
    private function array2xml(array $array, $xml = false)
    {
        (false === $xml) and $xml = new \SimpleXMLElement('<data/>');
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item';
            }
            if (is_array($value)) {
                $this->array2xml($value, $xml->addChild($key));
            } else {
                $xml->addChild($key, $value);
            }
        }
        return $xml->asXML();
    }

	
    /**
     * Get api url by method
     *
     * @param string $method Method name
     */
    private function getMethodUrl($method)
	{
		return $this->apiUrl . '/' . trim($method, '/ ');
	}
	
	
    /**
     * Make request to MeestExpress API.
     *
     * @param string $method Method name
     * @param array  $data Required data
     */
    public function request($method, $data=array(), $format=null)
    {
		$url = $this->getMethodUrl($method);
		
		if(is_null($format))
			$format = $this->format;
		
		$post = false;
        // Convert data to neccessary format
		if($data)
		{
			$post = 'xml' == $format
				? $this->array2xml($data)
				: $post = json_encode($data);
		}

		$headers = array();
		$headers[] = 'Content-Type: '.('xml' == $format ? 'text/xml' : 'application/json');
		$headers[] = 'token: ' . $this->getToken();
		
        if ('curl' == $this->getConnectionType()) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			if($post)
			{
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			}
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
			$http_stream = array(
				'header' => implode("\r\n", $headers)
			);
			if($post)
			{
				$http_stream['method'] = 'POST';
				$http_stream['content'] = $post;
			}
			
            $result = file_get_contents($url, null, stream_context_create(array(
                'http' => $http_stream,
            )));
        }

        return new Result($result, $format, $this->throwErrors);
    }

	
	
	
    public function search()
    {
        return new Search($this);
    }
	
	

	
	
	
	
}