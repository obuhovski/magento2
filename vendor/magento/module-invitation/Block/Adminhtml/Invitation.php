<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Invitation Adminhtml Block
 *
 */
namespace Magento\Invitation\Block\Adminhtml;

class Invitation extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize invitation manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_invitation';
        $this->_blockGroup = 'Magento_Invitation';
        $this->_headerText = __('Invitations');
        $this->_addButtonLabel = __('Add Invitations');
        parent::_construct();
    }

    /**
     * Returns the Css class string for the container
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-invitation';
    }
}
