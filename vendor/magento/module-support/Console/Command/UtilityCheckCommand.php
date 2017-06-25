<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Command for checking list of required utilities
 */
class UtilityCheckCommand extends AbstractBackupCommand
{
    /**
     * Name of input argument
     */
    const INPUT_KEY_HIDE_PATHS = 'hide-paths';

    /**
     * @deprecated since 2.1.0
     */
    const INPUT_KEY_UTILITIES = 'utilities';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('support:utility:check')
            ->setDescription('Check required backup utilities')
            ->setDefinition([
                new InputOption(
                    self::INPUT_KEY_HIDE_PATHS,
                    null,
                    InputOption::VALUE_NONE,
                    'Only check required console utilities'
                ),
            ]);
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->shellHelper->setRootWorkingDirectory();
            $this->shellHelper->initPaths();

            if (!$input->getOption(self::INPUT_KEY_HIDE_PATHS)) {
                $output->writeln('Utilities list:');
                foreach ($this->shellHelper->getUtilities() as $name => $path) {
                    $output->writeln(sprintf('%s => %s', $name, $path));
                }
            }
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
        }
    }
}
