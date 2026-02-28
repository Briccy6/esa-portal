<?php

return [

    /*
    |--------------------------------------------------------------------------
    | QR Code renderer
    |--------------------------------------------------------------------------
    |
    | Supported renderers:
    |  - 'imagick' (requires imagick PHP extension)
    |  - 'gd' (uses GD extension, default if imagick is missing)
    |  - 'svg'
    |
    */

    'renderer' => 'gd',

];
