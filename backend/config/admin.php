<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hidden Admin Login URI
    |--------------------------------------------------------------------------
    |
    | Admin accounts are stored separately from regular users. The login URL
    | is intentionally not linked anywhere in the public interface. Change it
    | through ADMIN_LOGIN_URI in .env when a different secret path is needed.
    |
    */

    'login_uri' => env('ADMIN_LOGIN_URI', '/admin-login'),

];
