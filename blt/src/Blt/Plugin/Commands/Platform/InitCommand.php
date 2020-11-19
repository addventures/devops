<?php

namespace Add\Blt\Plugin\Commands\Platform;

use Add\Blt\Plugin\Commands\BaseCommand;
use Symfony\Component\Console\Question\Question;

/**
 * Defines commands in the "stack:reset:*" namespace.
 */
class InitCommand extends BaseCommand {

    /**
     * Initializes a new platform or Drupal instance.
     *
     * @command platform:init
     */
    public function init($options = [
        'ni' => FALSE,
    ]) {
      $this->notice("Creating a new platform.");

      $config_sys = $this->getConfigSys();

      $inputs = [
        'name' => "Platform Name",
        'template' => "Template",
        'ace_app_id' => "Acquia Cloud Application ID",
      ];

      $defaults = [];

      foreach ($inputs as $option_id => $option_label) {

        if (!empty($options[$option_id])) {
          continue;
        }

        $default = isset($defaults[$option_id]) ? $defaults[$option_id] : NULL;
        $options[$option_id] = $this->io()->askQuestion(new Question("Enter {$option_label}", $default));

      }

      $this->success("New platform created.");
    }

}
