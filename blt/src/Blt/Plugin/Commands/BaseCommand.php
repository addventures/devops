<?php

namespace Add\Blt\Plugin\Commands;

use Acquia\Blt\Robo\BltTasks;
use Add\Blt\Plugin\Traits\IoTrait;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Base class for sys commands.
 */
class BaseCommand extends BltTasks {

  use IoTrait;

  /**
   * The path to the root directory of the current platform.
   *
   * @var string
   */
  protected $pathPlatform;

  /**
   * The path to sys root.
   *
   * @var string
   */
  protected $pathSys;

  /**
   * The path to user home folder.
   *
   * @var string
   */
  protected $pathHome;

  /**
   * The file system.
   *
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  protected $fs;

  /**
   * This hook will fire for all commands in this command file.
   *
   * @hook init
   */
  public function initialize() {
    $this->pathHome = getenv('HOME');
    $this->pathSys = "{$this->pathHome}/sys";
    $this->pathPlatform = $this->getConfigValue('repo.root');
    $this->fs = new Filesystem();
  }

  /**
   * @param array $config
   *
   * @throws \Exception
   */
  public function setConfigEnv(array $config) {
    $config_yaml = Yaml::dump($config);
    $this->fs->dumpFile($this->getPath('config_env'), $config_yaml);
  }

  /**
   * @return mixed
   * @throws \Exception
   */
  public function getConfigEnv() {
    $path_config_env = $this->getPath('config_env');
    if (!$this->fs->exists($path_config_env)) {
      return [];
    }
    $contents = file_get_contents($path_config_env);
    return Yaml::parse($contents);
  }

  /**
   * @param array $config
   *
   * @throws \Exception
   */
  public function setConfigSys(array $config) {
    $config_yaml = Yaml::dump($config);
    $this->fs->dumpFile($this->getPath('config_sys'), $config_yaml);
  }

  /**
   * @return mixed
   * @throws \Exception
   */
  public function getConfigSys() {
    $path_config_env = $this->getPath('config_sys');
    if (!$this->fs->exists($path_config_env)) {
      return [];
    }
    $contents = file_get_contents($path_config_env);
    return Yaml::parse($contents);
  }

  /**
   * @param null $path_id
   *
   * @return array|mixed|string
   * @throws \Exception
   */
  public function getPath($path_id = NULL) {
    $path = [];
    $path["config_env"] = "{$this->pathSys}/etc/env.yml";
    $path["config_sys"] = "{$this->pathSys}/project/devops/etc/sys.yml";
    $path["ssh"] = "{$this->pathHome}/.ssh";

    if (!empty($path_id)) {
      if (empty($path[$path_id])) {
        throw new \Exception("Path is not defined: {$path_id}");
      }
      return $path[$path_id];
    }

    return $path;
  }

  /**
   * @param array $inputs
   * @param array $return
   * @param array $defaults
   *
   * @return array
   * @throws \Exception
   */
  protected function buildInput(array $inputs, array $return = [], array $defaults = []) {
    foreach ($inputs as $option_id => $option_config) {

      if (!empty($return[$option_id])) {
        continue;
      }

      $default = isset($defaults[$option_id]) ? $defaults[$option_id] : NULL;

      if (!empty($option_config['type'])) {

        switch ($option_config['type']) {

          case 'choice':
            $return[$option_id] = $this->io()->choice($option_config['label'], $option_config['choice'], $default);
            break;

          default:
            throw new \Exception("Invalid input type: {$option_config['type']}");
            break;

        }

      }
      else {
        $return[$option_id] = $this->io()->askQuestion(new Question("Enter {$option_config['label']}", $default));
      }

    }
    return $return;
  }

}
