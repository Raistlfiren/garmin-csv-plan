<?php

namespace App\Library\Garmin;

use dawguk\GarminConnect;

class Client extends GarminConnect
{
    public function __construct(array $arrCredentials = array(), $garminUsername, $garminPassword)
    {
        parent::__construct([
            'username' => $garminUsername,
            'password' => $garminPassword,
        ]);
    }
}
