Laravel Hidden reCAPTCHA v3
==========
[![Packagist PHP Version](https://img.shields.io/packagist/php-v/esyede/hidden-recaptcha?style=flat-square)](https://packagist.org/packages/esyede/hidden-recaptcha)
[![Packagist Version](https://img.shields.io/packagist/v/esyede/hidden-recaptcha?style=flat-square)](https://packagist.org/packages/esyede/hidden-recaptcha)

## Support

Laravel 8+, PHP `>=7.4`.

## Installation

```
composer require esyede/hidden-recaptcha
```

### Setup

Add ServiceProvider to the providers array in `app/config/app.php`.

```
Esyede\HiddenReCaptcha\HiddenReCaptchaServiceProvider::class,
```

### Configuration

Adjust your `.env` file:

```
# required
INVISIBLE_RECAPTCHA_SITEKEY={siteKey}
INVISIBLE_RECAPTCHA_SECRETKEY={secretKey}

# optional
INVISIBLE_RECAPTCHA_BADGEHIDE=false
INVISIBLE_RECAPTCHA_DATABADGE='bottomright'
INVISIBLE_RECAPTCHA_TIMEOUT=5
INVISIBLE_RECAPTCHA_DEBUG=false
```

> There are three different captcha styles you can set: `bottomright`, `bottomleft`, `inline`

> If you set `INVISIBLE_RECAPTCHA_BADGEHIDE` to true, you can hide the badge logo.

> You can see the binding status of those catcha elements on browser console by setting `INVISIBLE_RECAPTCHA_DEBUG` as true.

### Usage

Before you render the captcha, please keep those notices in mind:

* `render()` or `renderHTML()` function needs to be called within a form element.
* You have to ensure the `type` attribute of your submit button has to be `submit`.
* There can only be one submit button in your form.

##### Display reCAPTCHA in Your View

```php
{!! app('captcha')->render() !!}

// or you can use this in blade
@captcha
```

With custom language support:

```php
{!! app('captcha')->render('en') !!}

// or you can use this in blade
@captcha('en')
```

##### Usage with Javascript frameworks like VueJS:

The `render()` process includes three distinct sections that can be rendered separately incase you're using the package with a framework like VueJS which throws console errors when `<script>` tags are included in templates.

You can render the polyfill (do this somewhere like the head of your HTML:)

```php
{!! app('captcha')->renderPolyfill() !!}
// Or with blade directive:
@captchaPolyfill
```

You can render the HTML using this following, this needs to be INSIDE your `<form>` tag:

```php
{!! app('captcha')->renderCaptchaHTML() !!}
// Or with blade directive:
@captchaHTML
```

And you can render the neccessary `<script>` tags including the optional language support by using:

```php
// The argument is optional.
{!! app('captcha')->renderFooterJS('en') !!}

// Or with blade directive:
@captchaScripts
// blade directive, with language support:
@captchaScripts('en')

```

##### Validation

Add `'g-recaptcha-response' => 'required|captcha'` to rules array.

```php
$validate = Validator::make(Input::all(), [
    'g-recaptcha-response' => 'required|captcha'
]);

```

## CodeIgniter 3.x

set in application/config/config.php :
```php
$config['composer_autoload'] = TRUE;
```

add lines in application/config/config.php :
```php
$config['recaptcha.sitekey'] = 'sitekey'; 
$config['recaptcha.secret'] = 'secretkey';
// optional
$config['recaptcha.options'] = [
    'hideBadge' => false,
    'dataBadge' => 'bottomright',
    'timeout' => 5,
    'debug' => false
];
```

In controller, use:
```php
$data['captcha'] = new \Esyede\HiddenReCaptcha\HiddenReCaptcha(
    $this->config->item('recaptcha.sitekey'),
    $this->config->item('recaptcha.secret'),
    $this->config->item('recaptcha.options'),
);
```

In view, in your form:
```php
<?php echo $captcha->render(); ?>
```

Then back in your controller you can verify it:
```php
$captcha->verifyResponse($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
```

## Without Laravel or CodeIgniter

Checkout example below:

```php
<?php

require_once "vendor/autoload.php";

$siteKey = 'sitekey';
$secretKey = 'secretkey';
// optional
$options = [
    'hideBadge' => false,
    'dataBadge' => 'bottomright',
    'timeout' => 5,
    'debug' => false
];
$captcha = new \Esyede\HiddenReCaptcha\HiddenReCaptcha($siteKey, $secretKey, $options);

// you can override single option config like this
$captcha->setOption('debug', true);

if (!empty($_POST)) {
    var_dump($captcha->verifyResponse($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']));
    exit();
}

?>

<form action="?" method="POST">
    <?php echo $captcha->render(); ?>
    <button type="submit">Submit</button>
</form>
```

## Take Control of Submit Function
Use this function only when you need to take all control after clicking submit button. Recaptcha validation will not be triggered if you return false in this function.

```javascript
_beforeSubmit = function(e) {
    console.log('submit button clicked.');
    // do other things before captcha validation
    // e represents reference to original form submit event
    // return true if you want to continue triggering captcha validation, otherwise return false
    return false;
}
```

## Customize Submit Function
If you want to customize your submit function, for example: doing something after click the submit button or changing your submit to ajax call, etc.

The only thing you need to do is to implement `_submitEvent` in javascript
```javascript
_submitEvent = function () {
    console.log('submit button clicked.');
    // write your logic here
    // submit your form
    _submitForm();
}
```
Here's an example to use an ajax submit (using jquery selector)
```javascript
_submitEvent = function() {
    $.ajax({
        type: "POST",
        url: "{{route('message.send')}}",
         data: {
            "name": $("#name").val(),
            "email": $("#email").val(),
            "content": $("#content").val(),
            // important! don't forget to send `g-recaptcha-response`
            "g-recaptcha-response": $("#g-recaptcha-response").val()
        },
        dataType: "json",
        success: function (data) {
            // success logic
        },
        error: function (data) {
            // error logic
        }
    });
};
```
