<?php

namespace Onamanzi\Mdcrypt;

use Illuminate\Support\ServiceProvider;

class MdcryptServiceProvider extends ServiceProvider {

	public function boot(){
		$this->publishes([
			$this->basePath('config/mdcrypt.php') => base_path('config/mdcrypt.php')
		],'mdcrypt-config');
	}

	public function register(){
		$this->app->bind('mdcrypt', function(){
			return new Mdcrypt;
		});

		$this->mergeConfigFrom(
			$this->basePath('config/mdcrypt.php'),
			'mdcrypt'
		);
	}

	protected function basePath($path = '') {
		return __DIR__ . '/../' . $path;
	}

}