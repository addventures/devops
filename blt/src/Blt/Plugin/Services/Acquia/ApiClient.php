<?php

namespace Add\Blt\Plugin\Services\Acquia;

use Acquia\Blt\Robo\BltTasks;
use Add\Blt\Plugin\Traits\IoTrait;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use AcquiaCloudApi\Connector\Client;
use AcquiaCloudApi\Connector\Connector;
use AcquiaCloudApi\Endpoints\Applications;
use AcquiaCloudApi\Endpoints\Environments;
use AcquiaCloudApi\Endpoints\Servers;
use AcquiaCloudApi\Endpoints\DatabaseBackups;
use AcquiaCloudApi\Endpoints\Variables;
use AcquiaCloudApi\Endpoints\Account;

/**
 * Provides a CLI service interface to typhonius/acquia-php-sdk-v2.
 */
class ApiClient {

  /**
   * @param string $api_context
   */
  public function getApiClient($api_context = 'applications') {
    $key = 'd0697bfc-7f56-4942-9205-b5686bf5b3f5';
    $secret = 'D5UfO/4FfNBWn4+0cUwpLOoFzfP7Qqib4AoY+wYGsKE=';

    $config = [
      'key' => $key,
      'secret' => $secret,
    ];

    $connector = new Connector($config);
    $client = Client::factory($connector);

    $application = new Applications($client);
    $environment = new Environments($client);
    $server      = new Servers($client);
    $backup      = new DatabaseBackups($client);
    $variable    = new Variables($client);
    $account     = new Account($client);
  }

}
