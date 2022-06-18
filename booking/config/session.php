<?php

use Illuminate\Support\Str;

return [

    /*
    Default Session Driver
    */

    'driver' => env('SESSION_DRIVER', 'file'),

    /*
    Session Lifetime
    */

    'lifetime' => env('SESSION_LIFETIME', 120),

    'expire_on_close' => false,


    'encrypt' => false,


    'files' => storage_path('framework/sessions'),

    /*
    Session Database Connection

    'connection' => env('SESSION_CONNECTION', null),

    /*
 Session Database Table
    */

    'table' => 'sessions',


    'store' => env('SESSION_STORE', null),


    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Name
    |--------------------------------------------------------------------------
    |
    | Here you may change the name of the cookie used to identify a session
    | instance by ID. The name specified here will get used every time a
    | new session cookie is created by the framework for every driver.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),



    'path' => '/',


    'domain' => env('SESSION_DOMAIN', null),


    'secure' => env('SESSION_SECURE_COOKIE'),



    'http_only' => true,


    'same_site' => 'lax',

];
