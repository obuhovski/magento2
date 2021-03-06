<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\ForeignKey\Strategy;

use Magento\Framework\DB\Adapter\AdapterInterface as Connection;
use Magento\Framework\ForeignKey\StrategyInterface;
use Magento\Framework\ForeignKey\ConstraintInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Restrict implements StrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(Connection $connection, ConstraintInterface $constraint, $condition)
    {
        throw new LocalizedException(new Phrase('Cannot add or update a child row: a foreign key constraint fails'));
    }

    /**
     * {@inheritdoc}
     */
    public function lockAffectedData(Connection $connection, $table, $condition, $fields)
    {
        $select = $connection->select()
            ->forUpdate(true)
            ->from($table, $fields)
            ->where($condition);

        $affectedData = $connection->fetchAssoc($select);

        if (!empty($affectedData)) {
            throw new LocalizedException(
                new Phrase('Cannot add or update a child row: a foreign key constraint fails')
            );
        }
        return $affectedData;
    }
}
