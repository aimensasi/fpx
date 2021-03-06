<?php

namespace Aimensasi\FPX;

use Aimensasi\FPX\Commands\UpdateBankListCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FPXServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap the application services.
	 */
	public function boot() {
		$this->configureRoutes();

		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'fpx');

		$this->configureComponents();

		$this->configurePublish();

		$this->registerEvents();
	}

	/**
	 * Register the application services.
	 */
	public function register() {
		// Automatically apply the package configuration
		$this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'fpx');
	}

	public function configureComponents() {
		Blade::component('fpx::components.pay', 'fpx-pay');
	}

	public function configureRoutes() {
		Route::group([
			'middleware' => Config::get('fpx.middleware')
		], function () {
			$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
		});
	}

	public function configurePublish() {
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../config/config.php' => config_path('fpx.php'),
			], 'config');

			$this->publishes([
				__DIR__ . '/../stubs/Controller.php' => app_path('Http/Controllers/FPX/Controller.php'),
			], 'fpx-controller');

			$this->commands([
				UpdateBankListCommand::class
			]);
		}
	}

	public function registerEvents() {
		// Event::listen(
		// 	PodcastProcessed::class,
		// 	[SendPodcastNotification::class, 'handle']
		// );
	}
}
