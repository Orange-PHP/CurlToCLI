<?php declare(strict_types=1);

/**
 * This file is part of CurlToCLI, an OrangePHP, LLC, Project.
 *
 * Copyright © 2022 Orange PHP, LLC.
 *
 * This file is licensed under the MIT License.
 */

namespace OrangePHP;

use OrangePHP\CurlToCLI\NotImplementedException;

$code = <<<'PHP'
$handler = OrangePHP\curl_init($this->endpoint . $url);
OrangePHP\curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
OrangePHP\curl_setopt($handler, CURLOPT_TIMEOUT, 65);

OrangePHP\curl_setopt($handler, CURLOPT_POSTFIELDS, $jsonData);
OrangePHP\curl_setopt($handler, CURLOPT_POST, 1);

OrangePHP\curl_setopt($handler, CURLOPT_CUSTOMREQUEST, $customRequest);
OrangePHP\curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
OrangePHP\curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, $this->verifySSL);

$response = OrangePHP\curl_exec($handler);
$httpCode = OrangePHP\curl_getinfo($handler, CURLINFO_HTTP_CODE);

$curlCLICommand = OrangePHP\convert_to_cli();
error_log("CURL CLI: $curlCLICommand");    
PHP;

class CurlToCLI
{
    public static $url = '';
    public static $headers = [];

    /** @var int Timeout in seconds. */
    public static $timeout = 300;

    public static $requestType;

    /** @var string */
    private static $requestData;

    /** @var ?string If null, verify SSL. If false, use insecure. */
    private static $sslVerifyPeer = null;

    /**
     * @param mixed $value
     * @return void
     */
    public static function processPostData($value)
    {
        if (is_string($value)) {
            self::$requestData = $value;
        } else {
            self::$requestData = http_build_query($value);
        }
    }

    public static function verifySSLPeer($value)
    {
        if ($value == false) {
            CurlToCLI::$sslVerifyPeer = '--insecure';
        } else {
            CurlToCLI::$sslVerifyPeer = null;
        }
    }

    public static function convertToCLI(): string
    {
        $headers = '';
        foreach (self::$headers as $header) {
            $header = escapeshellarg((string) $header);
            $headers .= "-H $header ";
        }
        $headers = rtrim($headers);

        $requestBody = '';
        if (!empty(self::$requestData)) {
            $requestData = escapeshellarg(self::$requestData);
            $requestBody = "-d $requestData";
        }

        $cmd = sprintf(
            'curl %s %s %s %s %s',
            self::$sslVerifyPeer,
            $headers,
            self::$requestType,
            escapeshellarg(self::$url),
            $requestBody
        );

        return $cmd;
    }
}

function curl_init(?string $url = null)
{
    CurlToCLI::$url = $url;

    return \curl_init($url);
}

function curl_setopt($handle, int $option, $value): bool
{
    switch ($option) {
        case CURLOPT_HTTPHEADER:
            if (is_array($value)) {
                CurlToCLI::$headers += $value;
            } else {
                CurlToCLI::$headers[] = $value;
            }
        break;
        case CURLOPT_TIMEOUT: CurlToCLI::$timeout = $value; break;

        case CURLOPT_POST: CurlToCLI::$requestType = '-X POST'; break;
        case CURLOPT_PUT: CurlToCLI::$requestType = '-X PUT'; break;
        case CURLOPT_CUSTOMREQUEST: CurlToCLI::$requestType = "-X $value"; break;

        case CURLOPT_POSTFIELDS: CurlToCLI::processPostData($value);

        case CURLOPT_SSL_VERIFYPEER: CurlToCLI::verifySSLPeer($value); break;

        // CURL options to ignore.
        case CURLOPT_RETURNTRANSFER: break;

        default: throw new NotImplementedException("That curl option is not implemented yet.");
    };

    return \curl_setopt($handle, $option, $value);
}

// BEGIN Passthrough Functions
function curl_exec($handle)
{
    return \curl_exec($handle);
}

function curl_getinfo($handle, ?int $option = null)
{
    return \curl_getinfo($handle, $option);
}
// END Passthrough Functions

function convert_to_cli(): string
{
    return CurlToCLI::convertToCLI();
}
