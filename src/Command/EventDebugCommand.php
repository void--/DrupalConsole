<?php

/**
 * @file
 * Contains \Drupal\Console\Command\EventDebugCommand.
 */

namespace Drupal\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Command\ContainerAwareCommand;
use Drupal\Console\Style\DrupalStyle;

/**
 * Class EventDebugCommand.
 *
 * @package Drupal\Console\Command
 */
class EventDebugCommand extends ContainerAwareCommand {
  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('event:debug')
      ->setDescription($this->trans('command.event.debug.description'));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

    $io = new DrupalStyle($input, $output);

    $io->section(
      $this->trans('commands.event.debug.messages.')
    );

    $tableHeader = [
      $this->trans('commands.event.debug.messages.')
    ];

    $tableRows = $this->getEvents();

    $io->table($tableHeader, $tableRows, 'compact');
  }
}
