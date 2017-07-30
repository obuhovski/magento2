<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ewave\EventGenerateReport\Controller\Adminhtml\Report\Club;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportEventCsv extends \Magento\Reports\Controller\Adminhtml\Report\AbstractReport
{
    /**
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'sales.csv';
        $grid = $this->_view->getLayout()->createBlock('Ewave\EventGenerateReport\Block\Adminhtml\Club\Event\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile(), DirectoryList::VAR_DIR);
    }
}
