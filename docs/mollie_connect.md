![Mollie](https://www.mollie.nl/files/Mollie-Logo-Style-Small.png)

# Using Mollie Connect with Laravel Socialite (Oauth)

[Mollie Connect](https://docs.mollie.com/oauth/overview) allows you to create apps for Mollie merchants via OAuth.

## Why should I use OAuth?
Mollie Connect is built on the [OAuth standard](https://en.wikipedia.org/wiki/OAuth). The OAuth connection enables you to access other merchants’ accounts with their consent, without having to exchange API keys. Whether you are just looking to improve your customers’ experiences, to automate essential business processes, or to whitelabel our platform completely, it is all possible with OAuth.

Our OAuth platform allows you to:

- Create payments and refunds on behalf of merchants
- Manage merchants’ website profiles
- View a merchants’ transaction and settlement data
- Show the next settlement and balance at Mollie in your app
- Integrate merchants’ invoices from Mollie in your app
- Charge merchants for payments initiated through your app ([application fees](https://docs.mollie.com/oauth/application-fees))

## Installation

Make sure the Laravel Socialite package is installed.

Then update `config/services.php` by adding this to the array:

```php
'mollie' => [
    'client_id' => env('MOLLIE_CLIENT_ID', 'app_xxx'),
    'client_secret' => env('MOLLIE_CLIENT_SECRET'),
    'redirect' => env('MOLLIE_REDIRECT_URI'),
],
```

Then add the corresponding credentials (`MOLLIE_CLIENT_ID`, `MOLLIE_CLIENT_SECRET`, `MOLLIE_REDIRECT_URI`) to your `.env` file.

## Example usage

```php
Route::get('login', function () {
    return Socialite::with('mollie')
        ->scopes(['profiles.read']) // Additional permission: profiles.read
        ->redirect();
});

Route::get('login_callback', function () {
    $user = Socialite::with('mollie')->user();

    Mollie::api()->setAccessToken($user->token);

    return Mollie::api()->profiles()->page(); // Retrieve payment profiles available on the obtained Mollie account
});
```

