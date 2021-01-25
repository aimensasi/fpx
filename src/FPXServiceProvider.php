<?php

namespace Aimensasi\FPX;

use Aimensasi\FPX\Commands\UpdateBankListCommand;
use Aimensasi\FPX\View\Components\PayComponent;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FPXServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap the application services.
	 */
	public function boot() {
		/*
		* Optional methods to load your package assets
		*/
		// $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'fpx');

		Route::group($this->routeConfiguration(), function () {
			$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
		});
		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'fpx');
		$this->configureComponents();

		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../config/config.php' => config_path('fpx.php'),
			], 'config');

			// Publishing the views.
			/*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/fpx'),
            ], 'views');*/

			// Publishing assets.
			/*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/fpx'),
            ], 'assets');*/

			// Publishing the translation files.
			/*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/fpx'),
            ], 'lang');*/

			// Registering package commands.
			$this->commands([
				UpdateBankListCommand::class
			]);
		}
	}

	/**
	 * Register the application services.
	 */
	public function register() {
		// Automatically apply the package configuration
		$this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'fpx');

		// Register the main class to use with the facade
		$this->app->singleton('FPX', function () {
			return new FPX;
		});
	}

	public function configureComponents() {
		Blade::component('fpx::components.pay', 'fpx-pay');
	}

	public function routeConfiguration() {
		return [
			'middleware' => Config::get('fpx.middleware')
		];
	}
}
