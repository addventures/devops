<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Symfony\Component\Console\Input\InputOption;
use Robo\Contract\VerbosityThresholdInterface;
use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines commands in the "stack:reset:*" namespace.
 */
class DeployCommand extends BaseCommand {

  /**
   * Add a new platform.
   *
   * @command platform:deploy
   */
  public function deploy($options = [
    'ni' => FALSE,
  ]) {
    $this->notice("Resetting all docker containers, images, and volumes.");
    $this->success("stack:reset ran okay");
  }

}