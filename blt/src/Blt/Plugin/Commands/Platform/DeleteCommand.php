<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Add\Blt\Plugin\Commands\BaseCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * Defines the "platform:delete" command.
 */
class DeleteCommand extends BaseCommand {

  /**
   * Delete an existing platform.
   *
   * @param array $options
   *   The command options.
   *
   * @command platform:delete
   */
  public function exec($options = [
    'platform' => InputOption::VALUE_OPTIONAL,
  ]) {

    if (empty($options['platform'])) {
      $options['platform'] = $this->io()->choice("Select a platform", $this->getOptionEnvPlatform());
    }

  }

}
