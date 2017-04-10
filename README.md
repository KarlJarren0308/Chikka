# Chikka

Chikka SMS API package for Laravel 5.

## Installation

Require this package in your project's `composer.json` file.

```
composer require karlmacz/chikka
```

## Setup

Add the service provider to the `providers` array in your project's `config/app.php` file.

```
KarlMacz\Chikka\ChikkaServiceProvider::class,
```

Add `CHIKKA_ACCESS_CODE`, `CHIKKA_CLIENT_ID`, `CHIKKA_SECRET_KEY`, and `CHIKKA_REQUEST_COST`in your project's `.env` file.

```
CHIKKA_ACCESS_CODE=your_short_code
CHIKKA_CLIENT_ID=your_client_id
CHIKKA_SECRET_KEY=your_secret_key
CHIKKA_REQUEST_COST=FREE
```

`CHIKKA_REQUEST_COST`'s default value is `FREE`, so no need to add this to your project's `.env` file if you want chikka to decide how much credits will be deducted to the user's actual load.

Also, `CHIKKA_STORE_TRANSACTIONS` is available with a default value of `false`. When set to `true`, all incoming and outgoing sms will be stored in the database. After setting it to `true`, publish the migrations from this package.

```
php artisan vendor:publish
```

This will add `2017_04_08_000000_create_chikka_incoming_sms_table.php` and `2017_04_08_000001_create_chikka_outgoing_sms_table.php` to your project's `database/migrations` directory. After that, run the migrations.

```
php artisan migrate
```

## Usage

### Sending SMS to a user

To send an sms, make a HTTP POST Request to the `/chikka/send` path.

```
<form action="/chikka/send" method="POST">
    <input type="hidden" name="message_type" value="SEND">
    <input type="text" name="mobile_number" required autofocus>
    <textarea name="message" required></textarea>
    <input type="submit" value="Send Message">
</form>
```

`message_type` parameter's value must be set to `SEND`.

### Replying to a SMS from users

To reply to an sms sent to you by the user, make a HTTP POST Request to the `/chikka/send` path with an additional paramater `request_id`.

```
<form action="/chikka/send" method="POST">
    <input type="hidden" name="message_type" value="REPLY">
    <input type="hidden" name="request_id" value="5048303030534D415254303030303032393230303032303030303030303133323030303036333933393932333934303030303030313331313035303735383137">
    <input type="text" name="mobile_number" required autofocus>
    <textarea name="message" required></textarea>
    <input type="submit" value="Send Message">
</form>
```

`message_type` parameter's value must be set to `REPLY`. Also, `request_id` parameter's value must be the same transaction ID indicated in the message you received.

`/chikka/send`'s named route `chikka.send` is also available for you to use.

### Receiving SMS from users

When `CHIKKA_STORE_TRANSACTIONS` is set to `true` in your project's `.env` file, all incoming sms will automatically be saved to the database.

All you have to do now is to add `/chikka/receive` path to the **Message Receiver URL** in the [Chikka API Settings Page](https://api.chikka.com/api/settings)

![Sample Image](https://github.com/KarlJarren0308/Chikka/blob/master/docs/images/chikka_api_settings.png)

## Note

When making a HTTP POST Request to the `/chikka/send` path using a Standard HTTP Request, the response is converted to a flash session that you may display by using `session()->get()`.

```
session()->get('flash_status')
session()->get('flash_message')
```

But, when making a HTTP POST Request to the `/chikka/send` path using AJAX, the response is encoded to JSON.

```
{ 'status': 'Success', 'message': 'Message sent.' }
```

## License
This package is licensed under the [MIT License](https://github.com/KarlJarren0308/Chikka/blob/master/LICENSE)
