# Laravel Recaptcha V3


<p align="center">
<a href="https://github.com/josiasmontag/laravel-recaptchav3/actions/workflows/run-tests.yml"><img src="https://github.com/josiasmontag/laravel-recaptchav3/actions/workflows/run-tests.yml/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/josiasmontag/laravel-recaptchav3"><img src="https://poser.pugx.org/josiasmontag/laravel-recaptchav3/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/josiasmontag/laravel-recaptchav3"><img src="https://poser.pugx.org/josiasmontag/laravel-recaptchav3/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/josiasmontag/laravel-recaptchav3"><img src="https://poser.pugx.org/josiasmontag/laravel-recaptchav3/license.svg" alt="License"></a>
</p>

Laravel package for Google's [Recaptcha V3](https://developers.google.com/recaptcha/docs/v3). This is a lightweight package which focuses on the backend validation of Recaptcha V3 captchas.

## Installation


To get started, use Composer to add the package to your project's dependencies:

    composer require josiasmontag/laravel-recaptchav3


Add `RECAPTCHAV3_SITEKEY` and `RECAPTCHAV3_SECRET` to your `.env` file. (You can get them [here](https://www.google.com/recaptcha/admin#list))

```
RECAPTCHAV3_SITEKEY=sitekey
RECAPTCHAV3_SECRET=secret
```

Optionally, you can publish the config file:
```
php artisan vendor:publish --provider="Lunaweb\RecaptchaV3\Providers\RecaptchaV3ServiceProvider"
```

## Usage

#### Init Recaptcha Javascript

Recaptcha v3 works best when it is loaded on every page to get the most context about interactions. Therefore, add to your header or footer template:

```php
{!! RecaptchaV3::initJs() !!}
```

#### Forms

``RecaptchaV3::field($action, $name='g-recaptcha-response')`` creates an invisible input field that gets filled with a Recaptcha token on load.


```html
<form method="post" action="/register">
    {!! RecaptchaV3::field('register') !!}
    <input type="submit" value="Register"></input>
</form>

```

#### Validation

Add the `recaptchav3` validator to the rules array. The rule accepts two parameters: The `action` name and the minimum required `score` (defaults to 0.5).

```php
$validate = Validator::make(Input::all(), [
	'g-recaptcha-response' => 'required|recaptchav3:register,0.5'
]);
```

#### Getting the score

Alternatively, you can get the score and take variable action:

```php
// Import the facade class
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
//  RecaptchaV3::verify($token, $action)
$score = RecaptchaV3::verify($request->get('g-recaptcha-response'), 'register')
if($score > 0.7) {
    // go
} elseif($score > 0.3) {
    // require additional email verification
} else {
    return abort(400, 'You are most likely a bot');
}
```

#### Custom validation error message

Add the following values to the `custom` array in the `validation` language file:

```php
'custom' => [
    'g-recaptcha-response' => [
        'recaptchav3' => 'Captcha error message',
    ],
],
```

#### Hiding the ReCAPTCHA Badge

Add to your CSS file:
```css
.grecaptcha-badge { visibility: hidden !important; }
```

#### Localization
By default, the package follows the default application locale, which is defined in `config/app.php`. If you want to change this behavior, you can specify what locale to use by adding a new environment variable : 
```
RECAPTCHAV3_LOCALE=ar
```

#### Testing

To make your forms testable, you can mock the `RecaptchaV3` facade:

```php
RecaptchaV3::shouldReceive('verify')
    ->once()
    ->andReturn(1.0);

```
