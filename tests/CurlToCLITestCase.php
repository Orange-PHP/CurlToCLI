<?php declare(strict_types=1);

/**
 * This file is part of CurlToCLI, an OrangePHP, LLC, Project.
 *
 * Copyright © 2022 Orange PHP, LLC.
 *
 * This file is licensed under the MIT License.
 */

namespace OrangePHP\CurlToCLI\Tests;

require_once __DIR__ . '/../src/CurlToCLI.php';

use PHPUnit\Framework\TestCase as BaseTestCase;

use \OrangePHP\curl_setopt;
use \OrangePHP\curl_getinfo;
use \OrangePHP\curl_exec;
use \OrangePHP\convert_to_cli;

class CurlToCLITestCase extends BaseTestCase
{
    /** @testdox Can convert a CURL POST request to a CLI command */
    public function testCanConvertACurlPostRequestToACliCommand()
    {
        $headers = [
            'Content-Type: application/json',
            'Content-Length: 150',
        ];
        $jsonData = [
            'actors' => [
                [
                    'name' => 'Samuel L. Jackson',
                    'age'  => 73,
                    'height' => 189,
                    'weight' => 83,
                ],
            ],
        ];
        $handler = \OrangePHP\curl_init('http://www.example3242444.com/');
        \OrangePHP\curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
        \OrangePHP\curl_setopt($handler, CURLOPT_TIMEOUT, 65);

        \OrangePHP\curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($jsonData));
        \OrangePHP\curl_setopt($handler, CURLOPT_POST, 1);

        \OrangePHP\curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'PUT');
        \OrangePHP\curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        \OrangePHP\curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);

        $response = \OrangePHP\curl_exec($handler);
        $httpCode = \OrangePHP\curl_getinfo($handler, CURLINFO_HTTP_CODE);

        $curlCLICommand = \OrangePHP\convert_to_cli();

        $expected =<<<'CMD'
curl --insecure -H 'Content-Type: application/json' -H 'Content-Length: 150' -X PUT 'http://www.example3242444.com/' -d '{"actors":[{"name":"Samuel L. Jackson","age":73,"height":189,"weight":83}]}'
CMD;
        self::assertEquals($expected, $curlCLICommand);
    }
}
