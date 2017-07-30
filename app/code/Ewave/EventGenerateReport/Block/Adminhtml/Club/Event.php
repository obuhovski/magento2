<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ewave\EventGenerateReport\Block\Adminhtml\Club;

use Ewave\Event\Api\EventRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Event
 * @package Ewave\EventGenerateReport\Block\Adminhtml\Club
 */
class Event extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Template file
     *
     * @var string
     */
    protected $_template = 'report/grid/container.phtml';

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        EventRepositoryInterface $eventRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->eventRepository = $eventRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Ewave_EventGenerateReport';
        $this->_controller = 'adminhtml_club_event';
        $this->_headerText = __('Total Event Report');
        parent::_construct();

        $this->buttonList->remove('add');
        $this->addButton(
            'filter_form_submit',
            ['label' => __('Show Report'), 'onclick' => 'filterFormSubmit()', 'class' => 'primary']
        );
    }

    /**
     * Get filter URL
     *
     * @return string
     */
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/event', ['_current' => true]);
    }

    /**
     * @return array
     */
    public function getClubIdEvents()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $events = $this->eventRepository->getList($searchCriteria)->getItems();

        $clubIdEvents = [];
        foreach ($events as $event) {
            if (!$event->getClubId()) {
                continue;
            }
            $clubIdEvents[$event->getClubId()][$event->getId()] = $event->getName() ;
        }

        return json_encode($clubIdEvents);
    }
}
