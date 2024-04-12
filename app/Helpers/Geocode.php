<?php

namespace App\Helpers;

use Spatie\Geocoder\Geocoder;

class Geocode
{
    private $geocoder;

    public function __construct()
    {
        $client = new \GuzzleHttp\Client();
        $this->geocoder = new Geocoder($client);
        $this->geocoder->setApiKey(config('geocoder.key'));
        $this->geocoder->setLanguage(config('geocoder.language', 'sw'));
    }

    public function getCoordinates($location)
    {
        $this->geocoder->setCountry(config('geocoder.country', 'TZ'));
        return $this->geocoder->getCoordinatesForAddress($location);
    }

    public function getAddressFromCoordinates($latitude, $longitude)
    {
        // $this->geocoder->setCountry(config('geocoder.country', 'TZ'));
        return $this->geocoder->getAddressForCoordinates($latitude, $longitude);
    }
}

