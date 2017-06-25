<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ForeignKey\Migration;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\App\DeploymentConfig\Writer as ConfigWriter;
use Magento\Framework\App\DeploymentConfig\Reader as ConfigReader;
use Magento\Framework\App\ResourceConnection\ConnectionFactory;
use Magento\Framework\DB\Adapter\Pdo\Mysql as MysqlAdapter;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractCommand extends Command
{
    /**
     * DB host name
     */
    const HOST = 'host';

    /**
     * Checkout DB name
     */
    const DB_NAME = 'dbname';

    /**
     * Checkout DB user
     */
    const USER_NAME = 'username';

    /**
     * Checkout DB user password
     */
    const PASSWORD = 'password';

    /**
     * New connection name
     */
    const CONNECTION = 'connection';

    /**
     * Linked resource name
     */
    const RESOURCE = 'resource';

    /**
     * @var ConfigWriter
     */
    private $configWriter;

    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * @var ConnectionFactory
     */
    private $connectionFactory;

    /**
     * Table names to migrate
     *
     * @var string[]
     */
    protected $tables;

    /**
     * Constructor.
     *
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     * @param ConfigWriter $configWriter
     * @param ConfigReader $configReader
     * @param ConnectionFactory $connectionFactory
     * @param string[] $tables
     * @throws \LogicException When the command name is empty
     *
     * @api
     */
    public function __construct(
        ConfigWriter $configWriter,
        ConfigReader $configReader,
        ConnectionFactory $connectionFactory,
        $tables,
        $name = null
    ) {
        $this->configWriter = $configWriter;
        $this->configReader = $configReader;
        $this->connectionFactory = $connectionFactory;
        $this->tables = $tables;
        parent::__construct($name);
    }

    /**
     * Get command name
     *
     * @return string
     */
    abstract protected function getCommandName();

    /**
     * Get command description
     *
     * @return string
     */
    abstract protected function getCommandDescription();

    /**
     * Get command definition
     *
     * @return string
     */
    abstract protected function getCommandDefinition();

    /**
     * Get table names to migrate
     *
     * @return string[]
     */
    protected function getTables()
    {
        return $this->tables;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->getCommandName())
            ->setDescription($this->getCommandDescription())
            ->setDefinition($this->getCommandDefinition());
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->generateConfig($input);

        $tables = [];
        foreach ($this->getTables() as $tableName) {
            $tables[] = $config['db']['table_prefix'] . $tableName;
        }

        // Get connections and disable foreign key checks
        $defaultConnection = $this->createConnection($config['db']['connection']['default']);
        $newConnection = $this->createConnection($config['db']['connection'][$input->getOption(self::CONNECTION)]);

        foreach ($tables as $tableName) {
            if ($defaultConnection->isTableExists($tableName)) {
                $this->moveTable($defaultConnection, $newConnection, $tableName);
            }
        }

        //04. Drop foreign keys between connections and enable foreign key checks
        $this->dropForeignKeys($newConnection, $defaultConnection);
        $this->dropForeignKeys($defaultConnection, $newConnection);

        $this->configWriter->saveConfig([ConfigFilePool::APP_ENV => $config], true);
        $output->writeln('Migration has been finished successfully!');
    }

    /**
     * Move table from first connection to second connection
     *
     * @param MysqlAdapter $firstConnection
     * @param MysqlAdapter $secondConnection
     * @param string $tableName
     * @return void
     */
    protected function moveTable(MysqlAdapter $firstConnection, MysqlAdapter $secondConnection, $tableName)
    {
        //Migrate schema to second connection
        $secondConnection->query($firstConnection->getCreateTable($tableName));

        //Migrate data to second connection
        $select = $firstConnection->select()->from($tableName);
        $data = $firstConnection->query($select)->fetchAll();
        if (count($data)) {
            $columns = array_keys($data[0]);
            $secondConnection->insertArray($tableName, $columns, $data);
        }

        //Drop table from first connection
        $firstConnection->dropTable($tableName);
    }

    /**
     * Drop foreign keys from firstConnection to secondConnection
     *
     * @param MysqlAdapter $firstConnection
     * @param MysqlAdapter $secondConnection
     * @return void
     */
    protected function dropForeignKeys(MysqlAdapter $firstConnection, MysqlAdapter $secondConnection)
    {
        foreach ($firstConnection->getTables() as $tableName) {
            foreach ($firstConnection->getForeignKeys($tableName) as $keyInfo) {
                if (in_array($keyInfo['REF_TABLE_NAME'], $secondConnection->getTables())) {
                    $firstConnection->dropForeignKey($keyInfo['TABLE_NAME'], $keyInfo['FK_NAME']);
                }
            }
        }
        $firstConnection->query('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Create DB connection
     *
     * @param array $config connection config
     * @return MysqlAdapter
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createConnection($config)
    {
        $connection = $this->connectionFactory->create($config);
        $connection->query('SET FOREIGN_KEY_CHECKS=0;');
        return $connection;
    }

    /**
     * Generate environment configuration
     *
     * @param InputInterface $input
     * @return array
     * @throws \Exception
     */
    protected function generateConfig(InputInterface $input)
    {
        $config = $this->configReader->load(ConfigFilePool::APP_ENV);

        if (!isset($config['db']['connection'][$input->getOption(self::CONNECTION)])) {
            $config['db']['connection'][$input->getOption(self::CONNECTION)] = [
                'host' => $input->getOption(self::HOST),
                'dbname' => $input->getOption(self::DB_NAME),
                'username' => $input->getOption(self::USER_NAME),
                'password' => $input->getOption(self::PASSWORD),
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1',
            ];
        }

        if (!isset($config['resource'][$input->getOption(self::RESOURCE)])) {
            $config['resource'][$input->getOption(self::RESOURCE)] = [
                'connection' => $input->getOption(self::CONNECTION)
            ];
        }
        return $config;
    }
}
