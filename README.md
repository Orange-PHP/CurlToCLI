# CurlToCLI (Curl To CLI)

CurlToCLI is a Orange PHP, LLC, Project meant to convert standard `curl_exec()` calls to `curl` CLI commands.

This makes it trivial to export even complex PHP curl calls to Postman, Insomnia or other dev teams.

## Installation

Via Composer

```bash
composer require --dev orangephp/curl-to-cli
```

## Usage

You must properly prepend the `OrangePHP\` namespace every single `curl_` function call.

Then call `OrangePHP\convert_to_cli();`

```php
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
```

## Use cases

 ✔ Rapidly start up a project right.  
 ✔ Less time spent on boilerplating a git repo.  
 ✔ Conforms to the most widely-deployed PHP layout.  
 ✔ Fully compatible with the Bettergist Collective recommendation.  

## Testing

```bash
phpunit --testdox
```

## License

MIT license. Please see the [license file](LICENSE) for more information.
:wq
