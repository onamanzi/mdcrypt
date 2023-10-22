<?php

namespace Onamanzi\Mdcrypt\Facades;

use Illuminate\Support\Facades\Facade;

class Mdcrypt extends Facade {

	/**
	 * Get the registered name of the component.
	 * 
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'mdcrypt';
	}
	
}