<?php
/**
 * Created by PhpStorm.
 * User: adminHOC
 * Date: 13/02/2019
 * Time: 11:40
 */

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class EmailService
{
    public function sendEmail($data)
    {
        $client = new Client();

        $headers = ['content-type' => "application/json"];
        $request = new Request('POST', 'http://169.51.4.250/email', $headers, json_encode($data));

        $response = $client->send($request);

        $code = $response->getStatusCode();

//        return $code;
        return $response->getBody()->getContents();
    }
}