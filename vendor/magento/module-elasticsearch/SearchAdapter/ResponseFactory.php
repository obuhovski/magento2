<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Elasticsearch\SearchAdapter;

use Magento\Framework\ObjectManagerInterface;

/**
 * Response Factory
 */
class ResponseFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Document Factory
     *
     * @var DocumentFactory
     */
    protected $documentFactory;

    /**
     * Aggregation Factory
     *
     * @var AggregationFactory
     */
    protected $aggregationFactory;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param DocumentFactory $documentFactory
     * @param AggregationFactory $aggregationFactory
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        DocumentFactory $documentFactory,
        AggregationFactory $aggregationFactory
    ) {
        $this->objectManager = $objectManager;
        $this->documentFactory = $documentFactory;
        $this->aggregationFactory = $aggregationFactory;
    }

    /**
     * Create Query Response instance
     *
     * @param array $response
     * @return \Magento\Framework\Search\Response\QueryResponse
     */
    public function create($response)
    {
        $documents = [];
        foreach ($response['documents'] as $rawDocument) {
            /** @var \Magento\Framework\Api\Search\Document[] $documents */
            $documents[] = $this->documentFactory->create(
                $rawDocument
            );
        }
        /** @var \Magento\Framework\Search\Response\Aggregation $aggregations */
        $aggregations = $this->aggregationFactory->create($response['aggregations']);
        return $this->objectManager->create(
            'Magento\Framework\Search\Response\QueryResponse',
            [
                'documents' => $documents,
                'aggregations' => $aggregations,
            ]
        );
    }
}
