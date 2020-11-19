<?php

namespace Add\Blt\Plugin\Commands\App;

use Symfony\Component\Console\Input\InputOption;
use Robo\Contract\VerbosityThresholdInterface;
use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines commands in the "stack:reset:*" namespace.
 */
class AddCommand extends BaseCommand {

  /**
   * Add a new app to a platform.
   *
   * @command app:add
   */
  public function add($options = [
    'ni' => FALSE,
  ]) {
    $this->notice("Resetting all docker containers, images, and volumes.");
    $this->success("stack:reset ran okay");
  }

}
