<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRule\Controller\Adminhtml\Targetrule;

class NewConditionHtml extends \Magento\TargetRule\Controller\Adminhtml\Targetrule
{
    /**
     * Ajax conditions
     *
     * @return void
     */
    public function execute()
    {
        $this->conditionsHtmlAction('conditions');
    }
}
