<?php

namespace App\Http\Controllers;

use App\Models\ShootingStar;
use Illuminate\Http\Request;

class ShootingStarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            return view('list');
    }


    public function list()
    {
        try
        {
            $url = "https://z9smj03u77.execute-api.us-east-1.amazonaws.com/stars";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            $headers = array(
            "Authorization: global",
            );
            
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            
            $resp = curl_exec($curl);
            curl_close($curl);
            $shootingsJson = json_decode($resp, true);
            $shootingStar = [];

            foreach ($shootingsJson as $obj) {
                switch ($obj["location"]) {
                    case 0: $location = "Asgarnia"; break;
                    case 1: $location = "Crandor or Karamja"; break;
                    case 2: $location = "Feldip Hills or on the Isle of Souls"; break;
                    case 3: $location = "Fossil Island or on Mos Le'Harmless"; break;
                    case 4: $location = "Fremennik Lands or on Lunar Isle"; break;
                    case 5: $location = "Great Kourend"; break;
                    case 6: $location = "Kandarin"; break;
                    case 7: $location = "Kebos Lowlands"; break;
                    case 8: $location = "Kharidian Desert"; break;
                    case 9: $location = "Misthalin"; break;
                    case 10: $location = "Morytania"; break;
                    case 11: $location = "Piscatoris or the Gnome Stronghold"; break;
                    case 12: $location = "Tirannwn"; break;
                    case 13: $location = "Wilderness"; break;                    
                    default: $location = "Unknown"; break;
                }

                if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARTDED_FOR'] != '') {
                    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } 
                else {
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                }

                if (!filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) )
                {
                    $timezone = "America/Porto_Velho";
                }
                else
                {
                    $ipInfo = file_get_contents('http://ip-api.com/json/' . $ip);                
                    $ipInfo = json_decode($ipInfo, true);
                    $timezone = $ipInfo["timezone"];
                }
                
                date_default_timezone_set($timezone);
         
                $minTime = date('H:i:s', $obj["minTime"]);
                $maxTime = date('H:i:s', $obj["maxTime"]);

                $shootingStar[] = [
                    'world' => $obj["world"],
                    'location' => $location,
                    'minTime' => $minTime,
                    'maxTime' => $maxTime,
                ];
            }
            
            return $shootingStar;
            }
            catch (Exception $e )
            {
                return $e;
            }
    }


   
}
