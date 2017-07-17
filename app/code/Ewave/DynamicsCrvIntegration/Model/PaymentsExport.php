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

class PaymentsExport extends EngineProcessor\ProcessorAbstract implements EngineProcessor\ProcessorInterface
{
    const FILE_PREFIX = 'Payment';
    const INTEGRATION_TYPE = 'pt';
    const FIELDS = [
        'ClubName',
        'EventName',
        'OrderNumber',
        'PaymentId',
        'CreatedDate',
        'CustomerFirstname',
        'CustomerEmail',
        'Subtotal',
        'OutstandingSmount',
        'DiscountAmount',
        'TotalPaid',
        'GST',
        'TransactionId',
        'PaymentMethod',
        'CreditCardType',
        'TransactionType',
    ];

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
     * @var ConnectionAdapterInterface
     */
    private $connection;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var FinancialTransaction
     */
    private $financialTransaction;

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

        $fileName = $this->exportEntitiesToFile($newIdsByTypes, $this->bunchSize);

        //TODO test ftp
//        $this->uploadExportedFileToFtp($fileName);

        $this->getLogger()->info('Sending file to ftp has been finished...');

        $this->logExportedEntities($newIdsByTypes);

        $this->getLogger()->info(
            'Payments Integration has been finished...'
        );

        return true;
    }

    /**
     * @param $newIdsByTypes
     * @param $bunchSize
     * @return string
     */
    public function exportEntitiesToFile($newIdsByTypes, $bunchSize)
    {
        $fileName = $this->getBackupFileName();
        $isBackupFileExist = file_exists($fileName);
        if (!$isBackupFileExist) {
            $this->addDataToCsv($fileName, [self::FIELDS]);
        }

        foreach ($newIdsByTypes as $type => $newIds) {
            $newIdChunks = array_chunk($newIds, $bunchSize);

            $this->getLogger()->info('Fetching from db to file has been started...');

            $this->exportEntitiesToFileByChunks($newIdChunks, $type);

            $this->getLogger()->info('Fetching from db to file has been finished...');
        }

        return $fileName;
    }

    /**
     * @return string
     */
    public function getBackupFileName()
    {
        return $this->backup->getBackupPath(self::INTEGRATION_TYPE) . self::FILE_PREFIX . date('dmy') . '.' . 'csv';
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

    public function exportEntitiesToFileByChunks($newIdChunks, $type)
    {
        $fileName = $this->getBackupFileName();

        $columnsForSelect = [
            'ClubName' => '(IF(!ISNULL(ec.name), ec.name, ec_via_eec.name))',
            'EventName' => '(IF( !ISNULL(ee.name), ee.name, ccev_name.value))',
            'OrderNumber' => 'so.increment_id',
            'PaymentId' => '(null)',
            'CreatedDate' => 'main_table.created_at',
            'CustomerFirstname' => '(ISNULL(so.customer_firstname, \'Guest\'))',
            'CustomerEmail' => '(null)',
            'Subtotal' => 'sub.base_row_total',
            'OutstandingSmount' => '(null)',
            'DiscountAmount' => 'sub.base_discount_amount',
            'TotalPaid' => 'sub.base_row_total_incl_tax',
            'GST' => 'sub.base_tax_amount',
            'TransactionId' => '(null)',
            'PaymentMethod' => 'sop.method',
            'CreditCardType' => 'sop.cc_type',
            'TransactionType' => '(\'' . $type . '\')',
        ];

        foreach ($newIdChunks as $ids) {
            $items = $this->financialTransaction->fetchEntitiesByType($ids, $type, $columnsForSelect);

            $this->addDataToCsv($fileName, $items);
            $this->getLogger()->info(
                "Fetched: \n\n" . var_export($items, true)
            );
        }

        return $fileName;
    }

    /**
     * @param array $newIdsByTypes
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

    }

    public function uploadExportedFileToFtp($fileName)
    {
        $this->ftp->connect(
            sprintf(
                'ftp://%s:%s@%s',
                $this->dataHelper->getPxFtpUsername(),
                $this->dataHelper->getPxFtpPassword(),
                $this->dataHelper->getPxFtpPath()
            )
        );

        $this->ftp->upload(
            '/',
            $fileName
        );
    }

    /**
     * @return bool|array
     */
    public function getNewDataIds()
    {
        return array_filter(
            [
                FinancialTransaction::TYPE_INVOICE => $this->financialTransaction->getNewDataIdForPt(
                    FinancialTransaction::TYPE_INVOICE
                ),
                FinancialTransaction::TYPE_RETURN => $this->financialTransaction->getNewDataIdForPt(
                    FinancialTransaction::TYPE_RETURN
                ),
            ]
        ) ?: false;
    }
}