<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Throwable;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LocatorController extends Controller
{
    public function __construct()
    {
        // $this->middleware('driverAuth');
    }
    public function getLocatorResults(Request $request) {
        $locatorUrl = "https://explorer.geowessex.com/plugins/general/core/HeaderBarSearchResults";
        $params = $request->all();
        $client = new Client([
            'defaults' => ['verify' => false, 'debug' => true]
        ]);
        try {
            $res = $client->post($locatorUrl, [
                'form_params' => $params
            ]);
        }
        catch (ClientException $e) {
            throw new Exception($e->getResponse()->getBody());
        }
        return $res->getBody();
    }
    public function convertbng2latlong(Request $request) {
        $params = $request->all();
        $apiUrl = 'https://api.getthedata.com/bng2latlong/'.$params['northing'].'/'.$params['easting'];

        $client = new Client();
        try {
            $res = $client->get($apiUrl, [
                'verify' => false
            ]);
        }
        catch (ClientException $e) {
            throw new Exception($e->getResponse()->getBody());
        }
        return $res->getBody();
    }
    public function getAreaData(Request $request) {
        $code = $request['code'];
        $query = "select * from `pc_".strtolower($code).";";
        $postcodes = DB::select($query);
        return response()->json(['postcodes' => $postcodes], 200, [], JSON_NUMERIC_CHECK);
    }
}
