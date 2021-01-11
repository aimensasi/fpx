<?php

namespace Aimensasi\FPX;

use App\View\Components\PayComponent;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class FPXServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap the application services.
	 */
	public function boot() {
		/*
		* Optional methods to load your package assets
		*/
		// $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'FPX');

		$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'fpx');
		$this->configureComponents();

		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../config/config.php' => config_path('FPX.php'),
			], 'config');

			// Publishing the views.
			/*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/FPX'),
            ], 'views');*/

			// Publishing assets.
			/*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/FPX'),
            ], 'assets');*/

			// Publishing the translation files.
			/*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/FPX'),
            ], 'lang');*/

			// Registering package commands.
			// $this->commands([]);
		}
	}

	/**
	 * Register the application services.
	 */
	public function register() {
		// Automatically apply the package configuration
		$this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'FPX');

		// Register the main class to use with the facade
		$this->app->singleton('FPX', function () {
			return new FPX;
		});
	}

	public function configureComponents() {
		$this->callAfterResolving(BladeCompiler::class, function () {
			Blade::component('fpx-pay', PayComponent::class);
			Blade::component('fpx::components.redirection-message', 'fpx-redirection-message');
		});
	}
}
