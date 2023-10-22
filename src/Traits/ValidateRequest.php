<?php

namespace Onamanzi\Mdcrypt\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait ValidateRequest {

	/** @var string */
	protected $validRequest;

	/** @var array */
	protected $requestErrors;

	/**
	 * Valida el request recibido
	 * 
	 * @param Illuminate\Http\Request
	 * @return boolean
	 */
	public function validateRequest(Request $rqt) {
		$dataRequest = $rqt->all();
		$valid = null;
		if (empty($dataRequest)) {
			$valid = false;
			$this->requestErrors = [
				'code' => 000,
				'message' => ['Error 000']//Faltan parámetros
			];
		}//if (empty($dataRequest))

		if (count($dataRequest) > 1) {
			$valid = false;
			$this->requestErrors = [
				'code' => 001,
				'message' => ['Error 001']//Mas de un parámetro
			];
		}//if (count($dataRequest) > 1)

		if (is_null($valid)) {
			foreach ($dataRequest as $data) {
				if (trim($data)) {
					$this->validRequest = $data;
					$valid = true;
				}else{
					$valid = false;
					$this->requestErrors = [
						'code' => 002,
						'message' => ['Error 002']//Request vacío
					];
				}
			}
		}
		return $valid;
	}

	/**
	 * Obtiene el valor del request
	 * 
	 * @return string
	 */
	public function getValidRequest() {
		return $this->validRequest;
	}

	/**
	 * Obtiene los errores del request
	 * 
	 * @return array
	 */
	public function getRequestErrors() {
		return $this->requestErrors;
	}

	/**
	 * Valida los campos del array en base
	 * a las reglas recibidas
	 * 
	 * @param array $data
	 * @param array $rules
	 * @return boolean
	 */
	public function validator(array $data,array $rules) {
		$validator = Validator::make($data,$rules);
		if ($validator->fails()) {
			$errorsArray = array();
			$errors = $validator->errors();
			foreach ($errors->all() as $message) {
				$errorsArray[] = $message;
			}
			$this->requestErrors = [
				'code' => 006,
				'message' => $errorsArray
			];
			return false;
		}//if ($validator->fails())
		return true;
	}



}