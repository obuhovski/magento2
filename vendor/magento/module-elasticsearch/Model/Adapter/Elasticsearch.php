<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Elasticsearch\Model\Adapter;

/**
 * Elasticsearch adapter
 */
class Elasticsearch
{
    /**#@+
     * Text flags for Elasticsearch bulk actions
     */
    const BULK_ACTION_INDEX = 'index';
    const BULK_ACTION_CREATE = 'create';
    const BULK_ACTION_DELETE = 'delete';
    const BULK_ACTION_UPDATE = 'update';
    /**#@-*/

    /**
     * @var \Magento\Elasticsearch\SearchAdapter\ConnectionManager
     */
    protected $connectionManager;

    /**
     * @var DataMapperInterface
     */
    protected $documentDataMapper;

    /**
     * @var \Magento\Elasticsearch\Model\Adapter\Index\IndexNameResolver
     */
    protected $indexNameResolver;

    /**
     * @var FieldMapperInterface
     */
    protected $fieldMapper;

    /**
     * @var \Magento\Elasticsearch\Model\Config
     */
    protected $clientConfig;

    /**
     * @var \Magento\Elasticsearch\Model\Client\Elasticsearch
     */
    protected $client;

    /**
     * @var \Magento\Elasticsearch\Model\Adapter\Index\BuilderInterface
     */
    protected $indexBuilder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $preparedIndex = [];

    /**
     * Constructor for Elasticsearch adapter.
     *
     * @param \Magento\Elasticsearch\SearchAdapter\ConnectionManager $connectionManager
     * @param DataMapperInterface $documentDataMapper
     * @param FieldMapperInterface $fieldMapper
     * @param \Magento\Elasticsearch\Model\Config $clientConfig
     * @param \Magento\Elasticsearch\Model\Adapter\Index\BuilderInterface $indexBuilder
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Elasticsearch\Model\Adapter\Index\IndexNameResolver $indexNameResolver
     * @param array $options
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Elasticsearch\SearchAdapter\ConnectionManager $connectionManager,
        DataMapperInterface $documentDataMapper,
        FieldMapperInterface $fieldMapper,
        \Magento\Elasticsearch\Model\Config $clientConfig,
        \Magento\Elasticsearch\Model\Adapter\Index\BuilderInterface $indexBuilder,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Elasticsearch\Model\Adapter\Index\IndexNameResolver $indexNameResolver,
        $options = []
    ) {
        $this->connectionManager = $connectionManager;
        $this->documentDataMapper = $documentDataMapper;
        $this->fieldMapper = $fieldMapper;
        $this->clientConfig = $clientConfig;
        $this->indexBuilder = $indexBuilder;
        $this->logger = $logger;
        $this->indexNameResolver = $indexNameResolver;

        try {
            $this->client = $this->connectionManager->getConnection($options);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We were unable to perform the search because of a search engine misconfiguration.')
            );
        }
    }

    /**
     * Retrieve Elasticsearch server status
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function ping()
    {
        try {
            $response = $this->client->ping();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Could not ping search engine: %1', $e->getMessage())
            );
        }
        return $response;
    }

    /**
     * Create Elasticsearch documents by specified data
     *
     * @param array $documentData
     * @param int $storeId
     * @return array
     */
    public function prepareDocsPerStore(array $documentData, $storeId)
    {
        $documents = [];
        if (count($documentData)) {
            foreach ($documentData as $documentId => $data) {
                $document = $this->documentDataMapper->map(
                    $documentId,
                    $data,
                    $storeId
                );
                $documents[$documentId] = $document;
            }
        }
        return $documents;
    }

    /**
     * Add prepared Elasticsearch documents to Elasticsearch index
     *
     * @param array $documents
     * @param int $storeId
     * @param string $mappedIndexerId
     * @return $this
     * @throws \Exception
     */
    public function addDocs(array $documents, $storeId, $mappedIndexerId)
    {
        if (count($documents)) {
            try {
                $indexName = $this->indexNameResolver->getIndexName($storeId, $mappedIndexerId, $this->preparedIndex);
                $bulkIndexDocuments = $this->getDocsArrayInBulkIndexFormat($documents, $indexName);
                $this->client->bulkQuery($bulkIndexDocuments);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw $e;
            }
        }

        return $this;
    }

    /**
     * Removes all documents from Elasticsearch index
     *
     * @param int $storeId
     * @param string $mappedIndexerId
     * @return $this
     */
    public function cleanIndex($storeId, $mappedIndexerId)
    {
        $this->checkIndex($storeId, $mappedIndexerId, true);
        $indexName = $this->indexNameResolver->getIndexName($storeId, $mappedIndexerId, $this->preparedIndex);
        if ($this->client->isEmptyIndex($indexName)) {
            // use existing index if empty
            return $this;
        }

        // prepare new index name and increase version
        $indexPattern = $this->indexNameResolver->getIndexPattern($storeId, $mappedIndexerId);
        $version = intval(str_replace($indexPattern, '', $indexName));
        $newIndexName = $indexPattern . ++$version;

        // remove index if already exists
        if ($this->client->indexExists($newIndexName)) {
            $this->client->deleteIndex($newIndexName);
        }

        // prepare new index
        $this->prepareIndex($storeId, $newIndexName, $mappedIndexerId);

        return $this;
    }

    /**
     * Delete documents from Elasticsearch index by Ids
     *
     * @param array $documentIds
     * @param int $storeId
     * @param string $mappedIndexerId
     * @return $this
     * @throws \Exception
     */
    public function deleteDocs(array $documentIds, $storeId, $mappedIndexerId)
    {
        try {
            $this->checkIndex($storeId, $mappedIndexerId, false);
            $indexName = $this->indexNameResolver->getIndexName($storeId, $mappedIndexerId, $this->preparedIndex);
            $bulkDeleteDocuments = $this->getDocsArrayInBulkIndexFormat(
                $documentIds,
                $indexName,
                self::BULK_ACTION_DELETE
            );
            $this->client->bulkQuery($bulkDeleteDocuments);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw $e;
        }

        return $this;
    }

    /**
     * Reformat documents array to bulk format
     *
     * @param array $documents
     * @param string $indexName
     * @param string $action
     * @return array
     */
    protected function getDocsArrayInBulkIndexFormat(
        $documents,
        $indexName,
        $action = self::BULK_ACTION_INDEX
    ) {
        $bulkArray = [
            'index' => $indexName,
            'type' => $this->clientConfig->getEntityType(),
            'body' => [],
            'refresh' => true,
        ];

        foreach ($documents as $id => $document) {
            $bulkArray['body'][] = [
                $action => [
                    '_id' => $id,
                    '_type' => $this->clientConfig->getEntityType(),
                    '_index' => $indexName
                ]
            ];
            if ($action == self::BULK_ACTION_INDEX) {
                $bulkArray['body'][] = $document;
            }
        }

        return $bulkArray;
    }

    /**
     * Checks whether Elasticsearch index and alias exists.
     *
     * @param int $storeId
     * @param bool $checkAlias
     * @param string $mappedIndexerId
     * @return $this
     */
    public function checkIndex(
        $storeId,
        $mappedIndexerId,
        $checkAlias = true
    ) {
        // create new index for store
        $indexName = $this->indexNameResolver->getIndexName($storeId, $mappedIndexerId, $this->preparedIndex);
        if (!$this->client->indexExists($indexName)) {
            $this->prepareIndex($storeId, $indexName, $mappedIndexerId);
        }

        // add index to alias
        if ($checkAlias) {
            $namespace = $this->indexNameResolver->getIndexNameForAlias($storeId, $mappedIndexerId);
            if (!$this->client->existsAlias($namespace, $indexName)) {
                $this->client->updateAlias($namespace, $indexName);
            }
        }
        return $this;
    }

    /**
     * Update Elasticsearch alias for new index.
     *
     * @param int $storeId
     * @param string $mappedIndexerId
     * @return $this
     */
    public function updateAlias($storeId, $mappedIndexerId)
    {
        if (!isset($this->preparedIndex[$storeId])) {
            return $this;
        }

        $oldIndex = $this->indexNameResolver->getIndexFromAlias($storeId, $mappedIndexerId);
        if ($oldIndex == $this->preparedIndex[$storeId]) {
            $oldIndex = '';
        }

        $this->client->updateAlias(
            $this->indexNameResolver->getIndexNameForAlias($storeId, $mappedIndexerId),
            $this->preparedIndex[$storeId],
            $oldIndex
        );

        // remove obsolete index
        if ($oldIndex) {
            $this->client->deleteIndex($oldIndex);
        }

        return $this;
    }

    /**
     * Create new index with mapping.
     *
     * @param int $storeId
     * @param string $indexName
     * @param string $mappedIndexerId
     * @return $this
     */
    protected function prepareIndex($storeId, $indexName, $mappedIndexerId)
    {
        $this->indexBuilder->setStoreId($storeId);
        $this->client->createIndex($indexName, ['settings' => $this->indexBuilder->build()]);
        $this->client->addFieldsMapping(
            $this->fieldMapper->getAllAttributesTypes(['entityType' => $mappedIndexerId]),
            $indexName,
            $this->clientConfig->getEntityType()
        );
        $this->preparedIndex[$storeId] = $indexName;
        return $this;
    }
}
