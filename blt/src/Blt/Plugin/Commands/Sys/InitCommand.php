<?php

namespace Add\Blt\Plugin\Commands\Sys;

use Symfony\Component\Console\Input\InputOption;
use Robo\Contract\VerbosityThresholdInterface;
use Add\Blt\Plugin\Commands\BaseCommand;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Finder\Finder;

/**
 * Defines commands in the "stack:init:*" namespace.
 */
class InitCommand extends BaseCommand {

  /**
   * Initializes the local host system.
   *
   * @param array $options
   *   The command options.
   *
   * @command sys:init
   * @throws \Exception
   */
  public function init($options = [
    'full_name' => FALSE,
    'email' => FALSE,
    'ace_api_key' => FALSE,
    'ace_api_secret' => FALSE,
    'github_token' => FALSE,
  ]) {
    $this->notice("Initializing local host system.");

    $inputs = [
      'full_name' => [
        'label' => "Full Name",
      ],
      'email' => [
        'label' => "Email Address",
      ],
      'ace_api_key' => [
        'label' => "Acquia Cloud API v2 Key (https://cloud.acquia.com/a/profile/tokens)",
      ],
      'ace_api_secret' => [
        'label' => "Acquia Cloud API v2 Secret (https://cloud.acquia.com/a/profile/tokens)",
      ],
      "github_token" => [
        'label' => "Github Personal Access Token (https://github.com/settings/tokens/new?scopes=repo,workflow&description=Addventures%20DevOps%20Tooling)",
      ]
    ];

    $defaults = $this->getConfigEnv();

    $options = $this->buildInput($inputs, $options, $defaults);

    $env_config = [];
    foreach ($inputs as $input_id => $input_label) {
      $env_config[$input_id] = $options[$input_id];
    }

    $env_config['platform'] = [];
    $env_config['project'] = [];

    // Validate private/public SSH key.
    $path_ssh = $this->getPath("ssh");
    $path_ssh_key = "{$path_ssh}/id_rsa";
    if (!$this->fs->exists($path_ssh_key)) {
      $this->notice("No SSH key could be found. Creating one.");
    }

    $path_ssh_keys = $this->getSshKeys();
    $ssh_key_path = $this->io()->choice("Which SSH key do you want to use for our projects", $path_ssh_keys, 0);
    if (isset($path_ssh_keys[$ssh_key_path])) {
      $ssh_key_path = $path_ssh_keys[$ssh_key_path];
    }

    $env_config['ssh']['key'] = $ssh_key_path;

    $this->setConfigEnv($env_config);

    $this->success("Finished initializing local host system. Start playing!");

  }

  /**
   * @return array
   * @throws \Exception
   */
  public function getSshKeys() {

    $ssh_key_paths = [];

    $path_ssh = $this->getPath("ssh");

    $finder = new Finder();
    $finder->in($path_ssh)
      ->files();

    foreach ($finder->getIterator() as $file_path => $file_info) {
      if (!fnmatch("*.pub", $file_path)) {
       continue;
      }

      $file_path_private_key = substr($file_path, 0, strlen($file_path) - 4);
      if ($this->fs->exists($file_path_private_key)) {
        $ssh_key_paths[] = $file_path_private_key;
      }
    }

    return $ssh_key_paths;
  }

}
