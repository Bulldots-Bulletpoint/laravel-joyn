<?php

namespace BulldotsBulletpoint\LaravelJoyn\App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class JoynController extends Controller
{

    public function getAuthorization()
    {
        $clientID = config('laravel-joyn.client_id');
        $clientSecret = config("laravel-joyn.client_secret");

        $client = new Client();

        $response = $client->post("https://api.joyn.eu/oauth/token", [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($clientID . ':' . $clientSecret),

            ], 'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function setLoyaltyToken($auth,$ref,$price)
    {

        $partner = config('laravel-joyn.partner_id');

        $token = $auth['access_token'];

        $jsonData = array(
            "amount" => $price,
            "partnerReference" => $partner,
            "transactionReference" => $ref,
        );

        $jsonDataEncoded = json_encode($jsonData);

        $client = new Client();

        $result = $client->post("https://api-v2.joyn.be/api/v2/partner/tokens", [
            'headers' => [
                'Authorization'=>'Bearer ' . $token,
                'Content-Type'=> 'application/json'
            ], 'body' => $jsonDataEncoded
        ]);

        return json_decode($result->getBody(), true);
    }
}
