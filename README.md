# Process feedback for email campaigns sent using Amazon SES

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-email-campaigns-ses-feedback.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-email-campaigns-ses-feedback)
[![Build Status](https://img.shields.io/travis/spatie/laravel-email-campaigns-ses-feedback/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-email-campaigns-ses-feedback)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-email-campaigns-ses-feedback.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-email-campaigns-ses-feedback)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-email-campaigns-ses-feedback.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-email-campaigns-ses-feedback)

This package is an add on for [spatie/laravel-email-campaigns](https://github.com/spatie/laravel-email-campaigns) that can process the feedback given by SES.

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-email-campaigns-ses-feedback
```

Under the hood this package uses [spatie/laravel-webhook-client](https://github.com/spatie/laravel-email-campaigns) to process handle webhooks. You are required to publish its migration to create the `webhook_calls` table. You can skip this step if your project already uses the `laravel-webhook-client` package directly.

```php
php artisan vendor:publish --provider="Spatie\WebhookClient\WebhookClientServiceProvider" --tag="migrations"
```

After the migration has been published, you can create the `webhook_calls` table by running the migrations:

```php
php artisan migrate
```

### Route

You must use this route macro somewhere in your routes file. Replace `'ses-feeback'` with the url you specified at SES when setting up the webhook there.

```php
Route::sesFeedback('ses-feedback');
```

### Simple Email Service
1. Create a configuration set in your AWS SES console if you haven't already
2. Add a SNS destination in the Event Destinations
    - Make sure to check the **bounce** type
3. Create a new topic for this destination

### Simple Notification Service
1. Create a subscription for the topic you just created, use `HTTPS` as the Protocol
2. Use the endpoint you just created the route for
3. Do **not** check "Enable raw message delivery", otherwise signature validation won't work
4. Your subscription should be automatically confirmed if the endpoint was reachable 

## Usage

After following the installation instructions, your project is ready to handle feedback by SES on the sent email campaigns.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing
    
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [Rias Van der Veken](https://github.com/riasvdv)
- [All Contributors](../../contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
