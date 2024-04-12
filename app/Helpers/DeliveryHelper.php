<?php

namespace App\Helpers;

use App\Models\V2\DeliveryPartner;
use Illuminate\Support\Facades\DB;

class DeliveryHelper {

    public static function calculateDeliveryFee($weight,$distance){
        //updating lookup table

        $deliveryRates = array(
            array(

                'weight' => 0.5,
                'distance' => 50,
                'delivery_fee' => 4885,

            ),
            array(

                'weight' => 0.5,
                'distance' => 149,
                'delivery_fee' => 7106,
            ),
            array('weight' => 0.5, 'distance' => 300, 'delivery_fee' => 8883),
            array('weight' => 0.5, 'distance' => 600, 'delivery_fee' => 11103),
            array('weight' => 0.5, 'distance' => 700, 'delivery_fee' => 14434),
            array('weight' => 0.5, 'distance' => 800, 'delivery_fee' => 16211),
            array('weight' => 0.5, 'distance' => 1500, 'delivery_fee' => 20430),
            array('weight' => 1, 'distance' => 50, 'delivery_fee' => 5774),
            array('weight' => 1, 'distance' => 149, 'delivery_fee' => 9327),
            array('weight' => 1, 'distance' => 300, 'delivery_fee' => 11103),
            array('weight' => 1, 'distance' => 600, 'delivery_fee' => 12880),
            array('weight' => 1, 'distance' => 700, 'delivery_fee' => 16211),
            array('weight' => 1, 'distance' => 800, 'delivery_fee' => 18209),
            array('weight' => 1, 'distance' => 1500, 'delivery_fee' => 22650),
            array('weight' => 2, 'distance' => 50, 'delivery_fee' => 7106),
            array('weight' => 2, 'distance' => 149, 'delivery_fee' => 10215),
            array('weight' => 2, 'distance' => 300, 'delivery_fee' => 12658),
            array('weight' => 2, 'distance' => 600, 'delivery_fee' => 14656),
            array('weight' => 2, 'distance' => 700, 'delivery_fee' => 17987),
            array('weight' => 2, 'distance' => 800, 'delivery_fee' => 20430),
            array('weight' => 2, 'distance' => 1500, 'delivery_fee' => 24871),
            array('weight' => 3, 'distance' => 50, 'delivery_fee' => 8883),
            array('weight' => 3, 'distance' => 149, 'delivery_fee' => 11103),
            array('weight' => 3, 'distance' => 300, 'delivery_fee' => 14212),
            array('weight' => 3, 'distance' => 600, 'delivery_fee' => 16433),
            array('weight' => 3, 'distance' => 700, 'delivery_fee' => 19764),
            array('weight' => 3, 'distance' => 800, 'delivery_fee' => 22650),
            array('weight' => 3, 'distance' => 1500, 'delivery_fee' => 27091),
            array('weight' => 4, 'distance' => 50, 'delivery_fee' => 11103),
            array('weight' => 4, 'distance' => 149, 'delivery_fee' => 12880),
            array('weight' => 4, 'distance' => 300, 'delivery_fee' => 16433),
            array('weight' => 4, 'distance' => 600, 'delivery_fee' => 18876),
            array('weight' => 4, 'distance' => 700, 'delivery_fee' => 21540),
            array('weight' => 4, 'distance' => 800, 'delivery_fee' => 24871),
            array('weight' => 4, 'distance' => 1500, 'delivery_fee' => 29312),
            array('weight' => 5, 'distance' => 50, 'delivery_fee' => 12880),
            array('weight' => 5, 'distance' => 149, 'delivery_fee' => 14656),
            array('weight' => 5, 'distance' => 300, 'delivery_fee' => 18876),
            array('weight' => 5, 'distance' => 600, 'delivery_fee' => 21540),
            array('weight' => 5, 'distance' => 700, 'delivery_fee' => 24871),
            array('weight' => 5, 'distance' => 800, 'delivery_fee' => 27328),
            array('weight' => 5, 'distance' => 1500, 'delivery_fee' => 34160),
            array('weight' => 6, 'distance' => 50, 'delivery_fee' => 14434),
            array('weight' => 6, 'distance' => 149, 'delivery_fee' => 16211),
            array('weight' => 6, 'distance' => 300, 'delivery_fee' => 20430),
            array('weight' => 6, 'distance' => 600, 'delivery_fee' => 22650),
            array('weight' => 6, 'distance' => 700, 'delivery_fee' => 26203),
            array('weight' => 6, 'distance' => 800, 'delivery_fee' => 29312),
            array('weight' => 6, 'distance' => 1500, 'delivery_fee' => 34160),
            array('weight' => 7, 'distance' => 50, 'delivery_fee' => 16211),
            array('weight' => 7, 'distance' => 149, 'delivery_fee' => 18209),
            array('weight' => 7, 'distance' => 300, 'delivery_fee' => 22650),
            array('weight' => 7, 'distance' => 600, 'delivery_fee' => 24871),
            array('weight' => 7, 'distance' => 700, 'delivery_fee' => 28424),
            array('weight' => 7, 'distance' => 800, 'delivery_fee' => 31533),
            array('weight' => 7, 'distance' => 1500, 'delivery_fee' => 36381),
            array('weight' => 8, 'distance' => 50, 'delivery_fee' => 18209),
            array('weight' => 8, 'distance' => 149, 'delivery_fee' => 20430),
            array('weight' => 8, 'distance' => 300, 'delivery_fee' => 24871),
            array('weight' => 8, 'distance' => 600, 'delivery_fee' => 27328),
            array('weight' => 8, 'distance' => 700, 'delivery_fee' => 30881),
            array('weight' => 8, 'distance' => 800, 'delivery_fee' => 34160),
            array('weight' => 8, 'distance' => 1500, 'delivery_fee' => 39008),
            array('weight' => 9, 'distance' => 50, 'delivery_fee' => 20430),
            array('weight' => 9, 'distance' => 149, 'delivery_fee' => 22650),
            array('weight' => 9, 'distance' => 300, 'delivery_fee' => 27328),
            array('weight' => 9, 'distance' => 600, 'delivery_fee' => 29312),
            array('weight' => 9, 'distance' => 700, 'delivery_fee' => 34160),
            array('weight' => 9, 'distance' => 800, 'delivery_fee' => 36381),
            array('weight' => 9, 'distance' => 1500, 'delivery_fee' => 41229),
            array('weight' => 10, 'distance' => 50, 'delivery_fee' => 22650),
            array('weight' => 10, 'distance' => 149, 'delivery_fee' => 24871),
            array('weight' => 10, 'distance' => 300, 'delivery_fee' => 29312),
            array('weight' => 10, 'distance' => 600, 'delivery_fee' => 31533),
            array('weight' => 10, 'distance' => 700, 'delivery_fee' => 36381),
            array('weight' => 10, 'distance' => 800, 'delivery_fee' => 39008),
            array('weight' => 10, 'distance' => 1500, 'delivery_fee' => 43856),
            array('weight' => 11, 'distance' => 50, 'delivery_fee' => 24871),
            array('weight' => 11, 'distance' => 149, 'delivery_fee' => 27328),
            array('weight' => 11, 'distance' => 300, 'delivery_fee' => 31533),
            array('weight' => 11, 'distance' => 600, 'delivery_fee' => 34160),
            array('weight' => 11, 'distance' => 700, 'delivery_fee' => 39008),
            array('weight' => 11, 'distance' => 800, 'delivery_fee' => 41229),
            array('weight' => 11, 'distance' => 1500, 'delivery_fee' => 46077),
            array('weight' => 12, 'distance' => 50, 'delivery_fee' => 27328),
            array('weight' => 12, 'distance' => 149, 'delivery_fee' => 29312),
            array('weight' => 12, 'distance' => 300, 'delivery_fee' => 34160),
            array('weight' => 12, 'distance' => 600, 'delivery_fee' => 36381),
            array('weight' => 12, 'distance' => 700, 'delivery_fee' => 41229),
            array('weight' => 12, 'distance' => 800, 'delivery_fee' => 43856),
            array('weight' => 12, 'distance' => 1500, 'delivery_fee' => 48704),
            array('weight' => 13, 'distance' => 50, 'delivery_fee' => 29312),
            array('weight' => 13, 'distance' => 149, 'delivery_fee' => 31533),
            array('weight' => 13, 'distance' => 300, 'delivery_fee' => 36381),
            array('weight' => 13, 'distance' => 600, 'delivery_fee' => 39008),
            array('weight' => 13, 'distance' => 700, 'delivery_fee' => 43856),
            array('weight' => 13, 'distance' => 800, 'delivery_fee' => 46077),
            array('weight' => 13, 'distance' => 1500, 'delivery_fee' => 50925),
            array('weight' => 14, 'distance' => 50, 'delivery_fee' => 31533),
            array('weight' => 14, 'distance' => 149, 'delivery_fee' => 34160),
            array('weight' => 14, 'distance' => 300, 'delivery_fee' => 39008),
            array('weight' => 14, 'distance' => 600, 'delivery_fee' => 41229),
            array('weight' => 14, 'distance' => 700, 'delivery_fee' => 46077),
            array('weight' => 14, 'distance' => 800, 'delivery_fee' => 48704),
            array('weight' => 14, 'distance' => 1500, 'delivery_fee' => 53552),
            array('weight' => 15, 'distance' => 50, 'delivery_fee' => 34160),
            array('weight' => 15, 'distance' => 149, 'delivery_fee' => 36381),
            array('weight' => 15, 'distance' => 300, 'delivery_fee' => 41229),
            array('weight' => 15, 'distance' => 600, 'delivery_fee' => 43856),
            array('weight' => 15, 'distance' => 700, 'delivery_fee' => 48704),
            array('weight' => 15, 'distance' => 800, 'delivery_fee' => 50925),
            array('weight' => 15, 'distance' => 1500, 'delivery_fee' => 55773),
        );

        foreach($deliveryRates as $row){
            if($row['weight'] == $weight && $row['distance'] <= $distance){
                $deliveryFee = $row['delivery_fee'];
                return $deliveryFee;
            }
        }
        return null;

        // Retrieve delivery rates from the database
        // $deliveryRates = DB::table('delivery_rates')->get();


        // foreach ($deliveryRates as $deliveryRate) {
        //     if ($deliveryRate->weight == $weight && $deliveryRate->distance <= $distance) {
        //         $deliveryFee = $deliveryRate->price;
        //         return $deliveryFee;
        //     }

        // }
        // return null;
    }
}
