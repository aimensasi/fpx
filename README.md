# Very short description of the package

This package provides laravel implementations for Paynet FPX services.

## Installation

You can install the package via composer:

```bash
composer require aimensasi/fpx
```

Then run the publish command to publish the config files and support controller

```bash
php artisan vendor:publish --provider="Aimensasi\FPX\FPXServiceProvider"
```

This will generate the following files

* The config file with default setup for you to override `fpx.php`
* The controller that will receive payment response and any host-to-host events `Http/Controllers/FPX/Controller.php`

## Setups

1. Add your redirect urls and your Seller and Exchange Id to the `.env` file.

```php
FPX_INDIRECT_URL=https://app.test/payments/fpx/callback
FPX_INDIRECT_PATH=payments/fpx/callback
FPX_DIRECT_URL=https://app.test/payments/fpx/direct-callback
FPX_DIRECT_PATH=payments/fpx/direct-callback

FPX_EXCHANGE_ID=
FPX_SELLER_ID=
```

2. After generating your certificates add them to your app. By default, we look for the certificates inside the following directives.

```php
'certificates' => [
	'uat' => [
		'disk' => 'local', // S3 or Local. Don't put your certificate in public disk
		'dir' => '/certificates/uat',
	],
	'production' => [
		'disk' => 'local', // S3 or Local. Don't put your certificate in public disk
		'dir' => '/certificates/prod',
	]
],
```

You can override the defaults by updating the config file.

3. Run migration to add the banks table

```bash
php artisan migrate
```


## Usage

1. First run the following commands to seed the banks list.

``` bash
php artisan fpx:banks
```

 you should schedule the fpx:banks Artisan command to run daily:

 ```php
 $schedule->command('fpx:banks')->daily();
 ```


2. Add one the `x-fpx-pay` component with the following attributes

``` php
 <x-fpx-pay
		:reference-id="$invoice->id"
		:datetime="$invoice->created_at->format('Ymdhms')"
		:amount="$invoice->total"
		:customer-name="$company->name"
		:customer-email="$company->owner->email"
		product-description="Salary Invoice">
```

During testing, you can use the `test-mode` attribute to override the provided amount to 'MYR 1.00'

``` php
 <x-fpx-pay
		:reference-id="$invoice->id"
		:datetime="$invoice->created_at->format('Ymdhms')"
		:amount="$invoice->total"
		:customer-name="$company->name"
		:customer-email="$company->owner->email"
		product-description="Salary Invoice"
		test-mode>
```

3. Handle the payment response in `Http/Controllers/FPX/Controller.php`

``` php

	/**
	 * This will be called after the user approve the payment
	 * on the bank side
	 * 
	 * @param Request $request
	 * @return Response
	 */
	public function callback(Request $request) {
		$response = $request->handle();

		// Update your order status
	}

	/**
	 * This will handle any direct call from FPX
	 * 
	 * @param Request $request
	 * @return string
	 */
	public function webhook(Request $request) {
		$response = $request->handle();

		// Update your order status

		return 'OK';
	}
```



### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email aimensasi@icloud.com instead of using the issue tracker.

## Credits

- [AIMEN.S.A.SASI](https://github.com/aimensasi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
