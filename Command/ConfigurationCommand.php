<?php

namespace Kryn\CmsBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigurationCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('kryncms:configuration:database')
            ->setDescription('Builds all propel models in kryn bundles.')
            ->addArgument('type', InputArgument::REQUIRED, 'database type: mysql|pgsql|sqlite')
            ->addArgument('database-name', InputArgument::REQUIRED, 'database name')
            ->addArgument('username', InputArgument::REQUIRED, 'database login username')
            ->addArgument('pw', InputArgument::OPTIONAL, "use '' to define a empty password")
            ->addArgument('server', InputArgument::OPTIONAL, 'hostname or ip')
            ->addArgument('port', InputArgument::OPTIONAL)
            ->setHelp('
You can set with this command configuration values inside the app/config/config.kryn.xml file.

It overwrites only options that you provide.

')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $systemConfig = $this->getKrynCore()->getSystemConfig(false);

        $database = $systemConfig->getDatabase(true);

        $mainConnection = $database->getMainConnection();

        $mainConnection->setType($input->getArgument('type'));
        $mainConnection->setName($input->getArgument('database-name'));
        $mainConnection->setUsername($input->getArgument('username'));

        if (null !== $input->getArgument('pw')) {
            $mainConnection->setPassword($input->getArgument('pw'));
        }

        if (null !== $input->getArgument('server')) {
            $mainConnection->setServer($input->getArgument('server'));
        }

        if (null !== $input->getArgument('port')) {
            $mainConnection->setPort($input->getArgument('port'));
        }

        $path = realpath($this->getApplication()->getKernel()->getRootDir().'/..') . '/app/config/config.kryn.xml';
        $systemConfig->save($path);

        $cache = realpath($this->getApplication()->getKernel()->getRootDir().'/..') . '/app/config/config.kryn.xml.cache.php';
        @unlink($cache);

        $output->writeln(sprintf('File `%s` updated.', $path));
    }
}
