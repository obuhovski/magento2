<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Model\Entity\Update\Action;

use Magento\Staging\Model\Entity\Update\Action\TransactionExecutorFactory;

class TransactionPool
{
    /**
     * @var TransactionExecutorFactory
     */
    private $transactionFactory;

    /**
     * @var array
     */
    private $transactionPool;

    /**
     * @param TransactionExecutorFactory $transactionExecutorFactory
     * @param array $transactionPool
     */
    public function __construct(
        TransactionExecutorFactory $transactionExecutorFactory,
        array $transactionPool = []
    ) {
        $this->transactionFactory = $transactionExecutorFactory;
        $this->transactionPool = $transactionPool;
    }

    /**
     * @param string $namespace
     * @return null|TransactionExecutorInterface
     */
    public function getExecutor($namespace)
    {
        if (in_array($namespace, $this->transactionPool)) {
            return $this->transactionFactory->create();
        }
    }
}
