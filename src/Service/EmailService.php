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

        $response = $client->request(
            'POST',
            'http://169.51.4.250/',
            $data
        );

        $code = $response->getStatusCode();

        return $code;
    }
}