<?php

namespace MeestExpress;

use Exception;

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
     * @var string Language of response
     */
    protected $language = 'ru';

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
     * @param string $language       Default Language
     * @param bool   $throwErrors    Throw request errors as Exceptions
     * @param bool   $connectionType Connection type (curl | file_get_contents)
     *
     * @return MeestExpress
     */
    public function __construct($login, $pasword, $language = 'ru', $throwErrors = false, $connectionType = 'curl')
    {
		$this->token = $this->getToken();
        $this->throwErrors = $throwErrors;
		
        return $this
            ->setLogin($login)
            ->setPassword($pasword)
            ->setLanguage($language)
            ->setConnectionType($connectionType);
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
     * Setter for language property.
     *
     * @param string $language
     *
     * @return MeestExpress
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Getter for language property.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
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

	

    public function setToken($token, $expire=0)
    {
		$this->token = $token;
		
		if($expire > 0)
			setcookie($this->getCookieStorageVar(), $this->token, time() + $expire);
		
        return $this;
    }
	
    public function getToken()
    {
		
		if(!empty($this->token))
			return $this->token;
		
		$cokie_var = $this->getCookieStorageVar();
		if(!empty($_COOKIE[$cokie_var]))
			return $_COOKIE[$cokie_var];

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
			$response = $this->_request('auth', $data);
			
			var_dump($response);
			
		}
        return $this;
    }

	
	
    /**
     * Prepare data before return it.
     *
     * @param json $data
     *
     * @return mixed
     */
    private function prepare($data)
    {
        //Returns array
        if ('array' == $this->format) {
            $result = is_array($data)
                ? $data
                : json_decode($data, 1);
            // If error exists, throw Exception
            if ($this->throwErrors and $result['errors']) {
                throw new Exception(is_array($result['errors']) ? implode("\n", $result['errors']) : $result['errors']);
            }
            return $result;
        }
        // Returns json or xml document
        return $data;
    }

    /**
     * Converts array to xml.
     *
     * @param array
     */
    private function array2xml(array $array, $xml = false)
    {
        (false === $xml) and $xml = new \SimpleXMLElement('<root/>');
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
     * Make request to MeestExpress API.
     *
     * @param string $model  Model name
     * @param string $method Method name
     * @param array  $params Required params
     */
    private function request($model, $method, $params = null)
    {
        // Get required URL

        $data = array(
            'apiKey' => $this->key,
            'modelName' => $model,
            'calledMethod' => $method,
            'language' => $this->language,
            'methodProperties' => $params,
        );

        return $this->prepare($result);
    }
	
	
    private function _getMethodUrl($method)
	{
		return $this->apiUrl . '/' . trim($method, '/ ');
	}
	
	
    /**
     * Make request to MeestExpress API.
     *
     * @param string $method Method name
     * @param array  $data Required data
     */
    private function _request($method, $data=array())
    {
		$url = $this->_getMethodUrl($method);
		
        // Convert data to neccessary format
        $post = 'xml' == $this->format
            ? $this->array2xml($data)
            : $post = json_encode($data);

        if ('curl' == $this->getConnectionType()) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: '.('xml' == $this->format ? 'text/xml' : 'application/json')));
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            $result = file_get_contents($url, null, stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded;\r\n",
                    'content' => $post,
                ),
            )));
        }

        return $this->prepare($result);
    }

}