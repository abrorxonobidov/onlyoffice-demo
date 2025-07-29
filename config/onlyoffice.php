<?php
/**
 * Created by PhpStorm
 * User: a_obidov
 * Date: 2025-07-29
 * Time: 11:37
 */

return [
  'server_url' => env('ONLYOFFICE_SERVER_URL', 'http://documentserver'),
  'secret' => env('ONLYOFFICE_JWT_SECRET'),
  'builder' => env('ONLYOFFICE_BUILDER_PATH'),
];
