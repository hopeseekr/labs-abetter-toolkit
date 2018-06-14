<?php

namespace ABetter\Toolkit;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {

		$this->loadViewsFrom(__DIR__.'/../views', 'abetter');

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
		//
    }

}
