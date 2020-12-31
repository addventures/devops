<?php

namespace Add\Blt\Plugin\Commands\Sys;

use Robo\Contract\VerbosityThresholdInterface;
use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines the "sys:reset" command.
 */
class ResetCommand extends BaseCommand {

  /**
   * Resets the full system.
   *
   * @param array $options
   *   The command options.
   *
   * @command sys:reset
   *
   * @throws \Robo\Exception\TaskException
   */
  public function exec($options = [
    'ni' => FALSE,
  ]) {
    $this->taskExecStack()
      ->dir($this->pathPlatform)
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE)
      ->stopOnFail()
      ->exec("docker rm -f $(docker ps -a -q)")
      ->exec("docker volume rm $(docker volume ls -q)")
      ->exec("docker rmi $(docker images -a -q)")
      ->run();
  }

}
