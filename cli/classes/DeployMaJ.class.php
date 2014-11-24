<?php

/**
 * $Id: DeployMaJ.class.php 21950 2014-02-06 15:42:21Z phenxdesign $
 *
 * @category CLI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision: 21950 $
 * @link     http://www.mediboard.org
 */

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\InvalidArgumentException;

/**
 * deploy:maj command
 */
class DeployMaJ extends DeployOperation {
  /**
   * @see parent::configure()
   */
  protected function configure() {
    $this
      ->setName('deploy:maj')
      ->setDescription('Synchronize MB with RSYNC')
      ->setHelp('Performs an RSYNC command from MB Master')
      ->addOption(
        'path',
        'p',
        InputOption::VALUE_OPTIONAL,
        'Working copy root',
        realpath(__DIR__ . "/../../")
      );
  }

  /**
   * @see parent::showHeader()
   */
  protected function showHeader(OutputInterface $output) {
    $output->writeln(
      <<<EOT
<fg=blue;bg=black>
       __  __     _        _
      |  \/  |   / \      | |
      | |\/| |  / _ \  _  | |
      | |  | | / ___ \| |_| |
      |_|  |_|/_/   \_\\\___/
</fg=blue;bg=black>
EOT
    );
  }

  /**
   * @see parent::testBranch()
   */
  protected function testBranch($master_branch, $release_code) {
    return ($master_branch == $release_code);
  }

  /**
   * @see paret::rsync()
   */
  protected function rsync($path, $files, $instance, OutputInterface $output, $dry_run = false) {
    $dialog = $this->getHelperSet()->get('dialog');

    $result = parent::rsync($path, $files, $instance, $output, true);

    if ($result) {
      $this->out($output, "<comment>These files will be updated:</comment>");

      foreach ($result as $_line) {
        $output->writeln(" > $_line");
      }

      $this->checkFilePresence(implode("\n", $result), $output);

      if (!$dialog->askConfirmation(
        $output,
        '<question>Confirm? [Y/n]</question>',
        true
      )
      ) {
        return false;
      }

      $result = parent::rsync($path, $files, $instance, $output);
      $this->checkFilePresence(implode("\n", $result), $output);
    }
    else {
      $this->out($output, "<comment>No file to update</comment>");
    }

    return $result;
  }

  /**
   * @see parent::execute()
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->showHeader($output);

    $path = $input->getOption('path');

    if (!is_dir($path)) {
      throw new InvalidArgumentException("'$path' is not a valid directory");
    }

    $current_branch = $this->getMasterBranch($path, $output);
    if (!$current_branch) {
      return;
    }

    $dialog    = $this->getHelperSet()->get('dialog');
    $instances = $this->promptInstances($path, $output, $dialog);

    $this->out($output, "Checking branches...");
    $instances_with_branch = $this->checkBranches($current_branch, $instances, $output);

    $this->out($output, "Result (<error>[branch]</error> will not be performed):\n");

    $to_perform = array();
    foreach ($instances_with_branch as $_instance) {
      $perform = "error";

      if ($_instance["perform"]) {
        $perform      = "info";
        $to_perform[] = $_instance["path"];
      }

      $output->writeln("- <$perform>" . $_instance["release_code"] . "</$perform> " . $_instance["path"]);
    }

    if (!$to_perform) {
      $this->out($output, "<error>No instance to update</error>");

      return;
    }

    $branch = $dialog->ask(
      $output,
      "\nConfirm operation by typing MASTER release code: "
    );

    if (!$branch || $branch !== $current_branch) {
      $output->writeln("<error>Wrong release code: $branch</error>");

      return;
    }

    $this->out($output, "<info>Confirmation OK</info>");
    $this->out($output, "Performing operation...");

    // External libraries installation
    $this->installLibraries($output);

    $files = $this->getIncludedAndExcluded($path, $output);

    foreach ($to_perform as $_instance) {
      // RSYNC
      $this->rsync($path, $files, $_instance, $output);
    }

    // Re-check remote release
    $this->out($output, "Current instances release:");
    $instances = $this->checkBranches($current_branch, $instances, $output);

    foreach ($instances as $_instance) {
      $perform = "error";

      if ($_instance["perform"]) {
        $perform = "info";
      }

      $output->writeln("- <$perform>" . $_instance["release_code"] . "</$perform> " . $_instance["path"]);
    }
  }
}
