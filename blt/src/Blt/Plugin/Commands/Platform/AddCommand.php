<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Symfony\Component\Console\Input\InputOption;
use Robo\Contract\VerbosityThresholdInterface;
use Add\Blt\Plugin\Commands\BaseCommand;

/**
 * Defines commands in the "stack:reset:*" namespace.
 */
class AddCommand extends BaseCommand {

  /**
   * Add a new platform.
   *
   * @param array $options
   *   The command options.
   *
   * @command platform:add
   */
  public function add($options = [
    'ni' => FALSE,
  ]) {
    $this->notice("Adding platform.");

    // Add hosts.

    $this->success("Added platform successfully.");
  }

}
