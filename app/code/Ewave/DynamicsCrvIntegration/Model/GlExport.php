<?php
namespace Ewave\DynamicsCrvIntegration\Model;

use Ewave\AI\Helper\Backup;
use Ewave\AI\Model\Engine\Processor as EngineProcessor;
use Ewave\AI\Model\Engine\Processor\Exception\ProcessException;
use Ewave\DynamicsCrvIntegration\Helper\Data;
use Ewave\DynamicsCrvIntegration\Model\ResourceModel\FinancialTransaction;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResourceConnection\ConnectionAdapterInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\System\Ftp;

class GlExport extends EngineProcessor\ProcessorAbstract implements EngineProcessor\ProcessorInterface
{
    const FILE_PREFIX = 'Payment';
    const TYPE_RETURN = 'Return';
    const TYPE_INVOICE = 'Invoice';
    const FIELDS = [
        'ClubName',
        'Date',
        'GLcode',
        'Debit',
        'Credit',
        'OrderNumber',
        'EventName',
        'EventDate',
        'AmountRefunded',
        'ProductType',
    ];
    const INTEGRATION_TYPE = 'gl';

    /**
     * @var File
     */
    protected $file;

    /**
     * @var Ftp
     */
    protected $ftp;

    /**
     * @var Backup
     */
    protected $backup;

    /**
     * @var mixed
     */
    protected $bunchSize;

    /**
     * @var FinancialTransaction
     */
    protected $financialTransaction;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var ConnectionAdapterInterface
     */
    protected $connection;

    /**
     * GlExport constructor.
     *
     * @param File $file
     * @param Ftp $ftp
     * @param Backup $backup
     * @param ResourceConnection $resourceConnection
     * @param Data $dataHelper
     * @param FinancialTransaction $financialTransaction
     * @param array $data
     * @param null $initParams
     */
    public function __construct(
        File $file,
        Ftp $ftp,
        Backup $backup,
        ResourceConnection $resourceConnection,
        Data $dataHelper,
        FinancialTransaction $financialTransaction,
        array $data = [],
        $initParams = null
    ) {
        parent::__construct($initParams);
        $this->file = $file;
        $this->ftp = $ftp;
        $this->backup = $backup;
        $this->connection = $resourceConnection->getConnection();
        $this->bunchSize = $data['bunchSize'];
        $this->dataHelper = $dataHelper;
        $this->financialTransaction = $financialTransaction;
    }

    /**
     * @return bool
     * @throws ProcessException
     * @throws \Ewave\AI\Model\Engine\Exception\EngineException
     */
    public function process()
    {
        $this->getLogger()->info(
            'Payments Integration has been started...'
        );

        $newIdsByTypes = $this->getNewDataIds();
        if ($newIdsByTypes === false) {
            $this->getLogger()->info('No new data.');

            return true;
        }

        $files = $this->exportEntitiesToFiles($newIdsByTypes, $this->bunchSize);

        //TODO test ftp
//        $this->uploadExportedFileToFtp($files);

        $this->getLogger()->info('Sending file to ftp has been finished...');

        $this->logExportedEntities($newIdsByTypes);
        $this->logExportedFiles($files);

        $this->getLogger()->info(
            'Payments Integration has been finished...'
        );

        return true;
    }

    /**
     * @param $newIdsByTypes
     * @param $bunchSize
     * @return array
     */
    public function exportEntitiesToFiles($newIdsByTypes, $bunchSize)
    {
        $files = [];
        foreach ($newIdsByTypes as $type => $newIds) {
            $newIdChunks = array_chunk($newIds, $bunchSize);

            $this->getLogger()->info('Fetching from db to file has been started...');

            $files += $this->exportEntitiesToFilesByChunks($newIdChunks, $type);

            $this->getLogger()->info('Fetching from db to file has been finished...');
        }

        return $files;
    }

    /**
     * @param $newIdChunks
     * @param $type
     * @return array
     */
    public function exportEntitiesToFilesByChunks($newIdChunks, $type)
    {
        $files = [];
        $backupPath = $this->backup->getBackupPath(self::INTEGRATION_TYPE);
        $columnsForSelect = array_combine(
            self::FIELDS,
            [
                'IF(!ISNULL(ec.name), ec.name, ec_via_eec.name)',
                "('" . date('d/m/Y h:i') . "')",
                '(NULL)',
                '(sub.base_row_total_incl_tax - sub.base_tax_amount)',
                '(sub.base_row_total_incl_tax - sub.base_tax_amount)',
                'so.increment_id',
                'IF(!ISNULL(ee.name), ee.name, ccev_name.value)',
                'DATE_FORMAT(ee.event_start_date, \'%d%m%y\')',
                0,
                '(null)',
            ]
        );
        $columnsForSelect += [
            'isAfterEventEnd' => '(DATE(ee.event_end_date) <  DATE(main_table.created_at))',
            'ecGlCode' => 'IF(!ISNULL(ec.gl_code), ec.gl_code, ec_via_eec.gl_code)',
            'eeGlCode' => 'ee.gl_code',
            'tmplGlCode' => 'ccev_gl_code.value',
            'isMembership' => '(ISNULL(ee.name))',
            'type' => '(\'' . $type . '\')',
        ];

        foreach ($newIdChunks as $ids) {
            $items = $this->financialTransaction->fetchEntitiesByType($ids, $type, $columnsForSelect);

            $separatedItems = $this->separateDebitsAndCredits($items);

            $filesData = array_reduce(
                $separatedItems, function ($carry, $item) {
                $fileName = $this->getFilename($carry, $item);
                $carry[$fileName][] = $item;

                return $carry;
            }, []
            );

            $this->saveFilesData($filesData, $backupPath);

            $files += array_keys($filesData);
        }

        return $files;
    }

    /**
     * @param array $items
     * @return array
     */
    public function separateDebitsAndCredits(array $items)
    {
        $newItems = [];
        foreach ($items as $item) {
            $isReturnType = $item['type'] === self::TYPE_RETURN;

            // Debit line
            $debit = $item;
            $debit['GLcode'] = sprintf(
                '%s-%s-%s',
                $item['ecGlCode'],
                $item['eeGlCode'] ?? 200,
                $item['tmplGlCode'] ?? 1120
            );
            $debit[$isReturnType ? 'Credit' : 'Debit'] = null;
            $newItems[] = $debit;

            // Credit line
            $credit = $item;
            $credit['GLcode'] = sprintf(
                '%s-%s-%s',
                $item['ecGlCode'],
                900,
                9025
            );
            $credit[$isReturnType ? 'Debit' : 'Credit'] = null;
            $newItems[] = $credit;
        }

        return $newItems;
    }

    /**
     * @param $carry
     * @param $item
     * @return string
     */
    public function getFilename($carry, $item)
    {
        if (!empty($item['isMembership'])) {
            return 'Membership' . date('dmy');
        }

        $fileName = 'GL' . $item['EventName'] . $item['EventDate'];
        if (empty($carry[$fileName])) {
            $select = $this->connection->select()
                ->from('ewave_dynamic_integration_exported_files_' . self::INTEGRATION_TYPE, 'file')
                ->where('file = ?', $fileName);
            $filenumber = count($this->connection->fetchCol($select));
            $fileName .= $filenumber ? ('-' . $filenumber) : '';
        }

        return $fileName;
    }

    /**
     * @param $filesData
     * @param $backupPath
     * @return $this
     */
    protected function saveFilesData($filesData, $backupPath)
    {
        $fieldsLength = count(self::FIELDS);
        $existFiles = [];

        foreach ($filesData as $filename => $fileData) {
            $filename = $backupPath . $filename . '.csv';
            if (empty($existFiles[$filename]) and !file_exists($filename)) {
                $existFiles[] = $filename;
                $this->addDataToCsv($filename, [self::FIELDS]);
            }
            $this->addDataToCsv(
                $filename,
                array_map(
                    function ($item) use ($fieldsLength) {
                        return array_slice($item, 0, $fieldsLength);
                    }, $fileData
                )
            );
        }

        return $this;
    }

    /**
     * @param string $file
     * @param array $data
     * @return $this
     */
    public function addDataToCsv($file, $data)
    {
        $fh = fopen($file, 'a');
        foreach ($data as $dataRow) {
            $this->file->filePutCsv($fh, $dataRow);
        }
        fclose($fh);

        return $this;
    }

    /**
     * @param $newIdsByTypes
     * @return $this
     */
    protected function logExportedEntities($newIdsByTypes)
    {
        foreach ($newIdsByTypes as $type => $newIds) {
            $dataForInsert = array_map(
                function ($id) use ($type) {
                    return [
                        $type,
                        $id,
                    ];
                }, $newIds
            );

            $this->connection->insertArray(
                'ewave_dynamic_integration_exported_entites_' . self::INTEGRATION_TYPE, [
                'type',
                'entity_id',
            ],
                $dataForInsert
            );
        }

        return $this;
    }

    /**
     * @param array $files
     * @return $this
     */
    protected function logExportedFiles(array $files)
    {
        $this->connection->insertArray(
            'ewave_dynamic_integration_exported_files_' . self::INTEGRATION_TYPE, [
            'file',
        ],
            array_map(
                function ($item) {
                    return explode('-', $item)[0];
                },
                $files
            )
        );

        return $this;
    }

    /**
     * @return bool|array
     */
    public function getNewDataIds()
    {
        return array_filter(
            [
                FinancialTransaction::TYPE_INVOICE => $this->financialTransaction->getNewDataIdsForGl(
                    FinancialTransaction::TYPE_INVOICE
                ),
                FinancialTransaction::TYPE_RETURN => $this->financialTransaction->getNewDataIdsForGl(
                    FinancialTransaction::TYPE_RETURN
                ),
            ]
        ) ?: false;
    }
}