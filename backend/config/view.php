<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Templat Blade disimpan di folder frontend agar tampilan dan aset
    | sumber tetap terpisah dari aplikasi Laravel di folder backend.
    |
    */

    'paths' => [
        realpath(base_path('../frontend/views')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | Semua templat Blade yang dikompilasi tetap disimpan di storage backend.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
