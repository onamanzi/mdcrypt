<?php

namespace Onamanzi\Mdcrypt;

use Onamanzi\Mdcrypt\Traits\ValidateRequest;

/**
 * Administrar la encriptación de los proyectos
 * 
 * @package mdcrypt
 * @author Norberto Sandoval <jnorsandoval@duck.com>
 */
class Mdcrypt {

	use ValidateRequest;

	/** @var string */
	protected $method;

	/** @var string */
	private $pass;

	/** @var string */
	private $iv;

	/** @var string */
	protected $project;

	/** @var string */
	protected $result;

	/** @var string */
	protected $status;

	/** @var string */
	protected $message;

	/** @var array */
	protected $errors;

	/**
	 * @param string $project
	 */
	public function __construct(string $project = ''){
		$this->method = config('mdcrypt.crypt_method');
		$this->project = $project;
		$this->setKeys();
	}

	/**
	 * Establece el nombre del proyecto
	 * 
	 * @param string $project
	 * @return $this
	 */
	public function setProject(string $project) {
		$this->project = $project;
		return $this;
	}

	/**
	 * Obtiene el nombre del proyecto
	 * 
	 * @param string $project
	 * @return string
	 */
	public function getProject() {
		return $this->project;
	}

	/**
	 * Obtiene el resultado
	 * 
	 * @return string
	 */
	public function getResult(string $type = 'string') {
		switch ($type) {
			case 'array':
			$dataJson = json_decode($this->result,true);
			if (is_null($dataJson)) {
				$this->status = "error";
				$this->message = "Encryption error";
				$this->errors = array(
					'code' => 003,
					'message' => ['Error 003']//Fallo al decodificar json
				);
				return false;
			}
			return $dataJson;
			break;
			default:
			return $this->result;
			break;
		}
	}

	/**
	 * Establece el método de encriptación (por defecto AES-128-CBC)
	 * 
	 * @param string $method
	 * @return $this
	 */
	public function setMethod(string $method) {
		$this->method = $method;
		return $this;
	}

	/**
	 * Obtiene el método de encriptación (por defecto AES-128-CBC)
	 * 
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * Establece las claves que se utilizarán en el cifrado
	 * 
	 * @param string $string
	 * @return $this
	 */
	public function setKeys(){
		$keys = config('mdcrypt.keys');
		if (array_key_exists($this->project,$keys['pass'])) {
			$this->pass = $keys['pass'][$this->project];
		}
		if (array_key_exists($this->project,$keys['iv'])) {
			$this->iv = $keys['iv'][$this->project];
		}
		return $this;
	}

	/**
	 * Encripta la cadena recibida y codifica en base64
	 * para que sea segura de usar en una url
	 * 
	 * @param string $string
	 * @return $this
	 */
	public function encrypt(string $string) {
		if ( (is_null($this->pass)) || is_null($this->iv) ) {
			$this->status = "error";
			$this->message = "Encryption error";
			$this->errors = array(
				'code' => 004,
				'message' => ['Error 004']//Fallo al encriptar
			);
			return $this;
		}
		try {
			$decryptedString = openssl_encrypt($string, $this->method, $this->pass, true, $this->iv);
		} catch (\Exception $e) {
			$this->status = "error";
			$this->message = $e->getMessage();
			$this->errors = array(
				'code' => 004,
				'message' => ['Error 004']//Fallo al encriptar
			);
			return $this;
		}
		$this->result = strtr( base64_encode($decryptedString), '+/=', '-_,' );
		if (!$this->result) {
			$this->status = "error";
			$this->message = "Encryption error";
			$this->errors = array(
				'code' => 004,
				'message' => ['Error 004']//Fallo al encriptar
			);
		}else{
			$this->status = "success";
			$this->message = "Encrypted string";
		}
		return $this;
	}

	/**
	 * Desencripta la cadena recibida y decodifica en base64
	 * 
	 * @param string $string
	 * @return $this
	 */
	public function decrypt(string $string) {
		if ( (is_null($this->pass)) || is_null($this->iv) ) {
			$this->status = "error";
			$this->message = "Decryption error";
			$this->errors = array(
				'code' => 005,
				'message' => ['Error 005']//Fallo al desencriptar
			);
			return $this;
		}
		try {
			$encryptedString = base64_decode(strtr($string, '-_,', '+/='));	
		} catch (\Exception $e) {
			$this->status = "error";
			$this->message = $e->getMessage();
			$this->errors = array(
				'code' => 005,
				'message' => ['Error 005']//Fallo al desencriptar
			);
			return $this;
		}
		$this->result = openssl_decrypt($encryptedString, $this->method, $this->pass, true, $this->iv);
		if (!$this->result) {
			$this->status = "error";
			$this->message = "Decryption error";
			$this->errors = array(
				'code' => 005,
				'message' => ['Error 005']//Fallo al desencriptar
			);
		}else{
			$this->status = "success";
			$this->message = "Decrypted string";
		}
		return $this;
	}

	public function getErrors() {
		return $this->errors;
	}

}