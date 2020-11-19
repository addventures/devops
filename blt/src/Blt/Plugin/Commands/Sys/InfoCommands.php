<?php

namespace Add\Blt\Plugin\Commands\Sys;

use Symfony\Component\Console\Input\InputOption;
use Robo\Contract\VerbosityThresholdInterface;
use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines commands in the "stack:init:*" namespace.
 */
class InfoCommands extends BaseCommand {

  /**
   * Prints a table of information about the full system.
   *
   * @command sys:info
   */
  public function info($options = [
    'ni' => FALSE,
  ]) {
    $this->say("Resetting all docker containers, images, and volumes.");

    $this->yell("stack:reset ran okay");
  }

}
