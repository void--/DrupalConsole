<?php
/**
 * @file
 * Contains \Drupal\AppConsole\Command\GeneratorPluginRestResourceCommand.
 */

namespace Drupal\AppConsole\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\AppConsole\Command\Helper\ServicesTrait;
use Drupal\AppConsole\Command\Helper\ModuleTrait;
use Drupal\AppConsole\Command\Helper\FormTrait;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Drupal\AppConsole\Generator\PluginRestResourceGenerator;
use Drupal\AppConsole\Command\Helper\ConfirmationTrait;

class GeneratorPluginRestResourceCommand extends GeneratorCommand
{
  use ServicesTrait;
  use ModuleTrait;
  use FormTrait;
  use ConfirmationTrait;

  protected function configure()
  {
    $this
      ->setDefinition([
        new InputOption('module','',InputOption::VALUE_REQUIRED, $this->trans('commands.common.options.module')),
        new InputOption('class-name','',InputOption::VALUE_OPTIONAL, $this->trans('commands.generate.plugin.rest.resource.options.class-name')),
        new InputOption('plugin-id','',InputOption::VALUE_OPTIONAL, $this->trans('commands.generate.plugin.rest.resource.options.plugin-id')),
        new InputOption('plugin-label','',InputOption::VALUE_OPTIONAL, $this->trans('commands.generate.plugin.rest.resource.options.plugin-label')),
        new InputOption('plugin-url','',InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, $this->trans('commands.generate.plugin.rest.resource.options.plugin-url')),
        new InputOption('plugin-states','',InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, $this->trans('commands.generate.plugin.rest.resource.options.plugin-states'))
      ])
    ->setDescription($this->trans('commands.generate.plugin.rest.resource.description'))
    ->setHelp($this->trans('commands.generate.plugin.rest.resource.help'))
    ->setName('generate:plugin:rest:resource');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $dialog = $this->getDialogHelper();

    // @see use Drupal\AppConsole\Command\Helper\ConfirmationTrait::confirmationQuestion
    if ($this->confirmationQuestion($input, $output, $dialog)) {
      return;
    }

    $module = $input->getOption('module');
    $class_name = $input->getOption('class-name');
    $plugin_id = $input->getOption('plugin-id');
    $plugin_label = $input->getOption('plugin-label');
    $plugin_url = $input->getOption('plugin-url');
    $plugin_states = $input->getOption('plugin-states');

    $this->getGenerator()
         ->generate($module, $class_name, $plugin_label, $plugin_id, $plugin_url, $plugin_states)
    ;
  }

  protected function interact(InputInterface $input, OutputInterface $output)
  {
    $dialog = $this->getDialogHelper();

    // --module option
    $module = $input->getOption('module');
    if (!$module) {
      // @see Drupal\AppConsole\Command\Helper\ModuleTrait::moduleQuestion
      $module = $this->moduleQuestion($output, $dialog);
    }
    $input->setOption('module', $module);

    // --class-name option
    $class_name = $input->getOption('class-name');
    if (!$class_name) {
      $class_name = $dialog->ask(
        $output,
        $dialog->getQuestion($this->trans('commands.generate.plugin.rest.resource.questions.class-name'), 'DefaultRestResource'),
        'DefaultRestResource'
      );
    }
    $input->setOption('class-name', $class_name);

    $machine_name = $this->getStringUtils()->camelCaseToUnderscore($class_name);

    // --plugin-label option
    $plugin_id = $input->getOption('plugin-id');
    if (!$plugin_id) {
      $plugin_id = $dialog->ask(
        $output,
        $dialog->getQuestion($this->trans('commands.generate.plugin.rest.resource.questions.plugin-id'), $machine_name),
        $machine_name
      );
    }
    $input->setOption('plugin-id', $plugin_id);

    // --plugin-id option
    $plugin_label = $input->getOption('plugin-label');
    if (!$plugin_label) {
      $plugin_label = $dialog->ask(
        $output,
        $dialog->getQuestion($this->trans('commands.generate.plugin.rest.resource.questions.plugin-label'),$machine_name),
        $machine_name
      );
    }
    $input->setOption('plugin-label', $plugin_label);

    // --plugin-url option
    $plugin_url = $input->getOption('plugin-url');
    if (!$plugin_url) {
      $plugin_url = $dialog->ask(
        $output,
        $dialog->getQuestion($this->trans('commands.generate.plugin.rest.resource.questions.plugin-url'),$machine_name),
        $machine_name
      );
    }
    $input->setOption('plugin-url', $plugin_url);


    // --plugin-states option
    $plugin_states = $input->getOption('plugin-states');
    if (!$plugin_states) {
      $questionHelper  = $this->getQuestionHelper();

      $question = new ChoiceQuestion(
        $this->trans('commands.generate.plugin.rest.resource.questions.plugin-states'),
          array('GET', 'PUT', 'POST', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'),
          '0'
      );

      $question->setMultiselect(true);
      $plugin_states = $questionHelper->ask($input, $output, $question);
      $output->writeln($this->trans('commands.generate.plugin.rest.resource.messages.selected-states') . ' ' . implode(', ', $plugin_states));

      $input->setOption('plugin-states', $plugin_states);
    }

  }

  protected function createGenerator()
  {
    return new PluginRestResourceGenerator();
  }

}