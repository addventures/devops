<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Add\Blt\Plugin\Commands\BaseCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Defines the "platform:add" command.
 */
class AddCommand extends BaseCommand {

  /**
   * Add a new platform.
   *
   * @param array $options
   *   The command options.
   *
   * @command platform:add
   *
   * @throws \Exception
   */
  public function exec($options = [
    'platform' => InputOption::VALUE_OPTIONAL,
  ]) {

    $inputs = [
      'platform' => [
        'label' => "Platform",
        'type' => 'choice',
        'choice' => $this->getOptionSysPlatform(),
      ],
    ];

    $defaults = [];

    $options = $this->buildInput($inputs, $options, $defaults);

    if (empty($options['id'])) {
      $options['id'] = $options['platform'];
    }

    if (empty($options['hostname'])) {
      $options['hostname'] = "{$options['id']}.local";
    }

    $platform_id_sys = $options['id'];
    $platform_config = $this->getConfigSys()->get("platform.{$platform_id_sys}");

    $platform_id_env = $this->buildEnvPlatformId($platform_id_sys);
    $virtual_host = "{$platform_id_env}.local";

    $base_path = !empty($platform_config['base_path']) ? $platform_config['base_path'] : NULL;
    $url = "http://{$virtual_host}{$base_path}";

    // Add to env.yml.
    $config_env = $this->getConfigEnv()->export();
    $config_env['platform'][$platform_id_env] = [
      'id' => $platform_id_env,
      'platform' => $options['platform'],
      'hostname' => $virtual_host,
      'url' => $url,
      'created' => $this->getDateString(),
      'role' => 'dev',
    ];
    $this->setConfigEnv($config_env);

    $git_url = $platform_config['git_url'];
    $path_platform = $this->getPath("platform.{$platform_id_env}");
    $path_docroot = "{$path_platform}/docroot";

    $this->taskExecStack()
      ->exec("git clone {$git_url} {$path_platform}")
      ->run();

    $ssh_key = $this->getConfigEnv()->get("ssh.key");
    $ssh_key_pieces = explode('/', $ssh_key);
    $ssh_key_agent = end($ssh_key_pieces);

    $this->taskExecStack()
      ->dir($path_platform)
      ->exec("fin config set VIRTUAL_HOST='{$virtual_host}'")
      ->exec("fin config set CLI_IMAGE=docksal/cli:2.11-php7.3")
      ->exec("fin hosts add")
      ->exec("fin p start")
      ->exec("fin ssh-key add {$ssh_key_agent}")
      ->exec("fin exec 'sudo composer self-update --1'")
      ->exec("fin exec 'composer install'")
      ->exec("fin exec 'echo y | /var/www/vendor/bin/blt sync:db'")
      ->exec("fin exec 'echo y | /var/www/vendor/bin/blt sync:files'")
      ->run();

    $result = $this->taskExecStack()
      ->dir($path_platform)
      ->exec("fin exec 'drush uli --uri=\"{$url}\"'")
      ->printOutput(FALSE)
      ->run();

    $url_uri = $result->getMessage();

    $this->taskExecStack()
      ->exec("open {$url_uri}")
      ->run();

  }

}
