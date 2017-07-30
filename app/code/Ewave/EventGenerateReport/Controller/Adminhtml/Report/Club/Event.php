<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ewave\EventGenerateReport\Controller\Adminhtml\Report\Club;

class Event extends \Magento\Reports\Controller\Adminhtml\Report\AbstractReport
{
    /**
     * @return ResponseInterface
     */
    public function execute()
    {

        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_club_event'
        )->_addBreadcrumb(
            __('Club and Event Report'),
            __('Club and Event Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Event Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_club_event.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
