<?php
namespace Ewave\EventGenerateReport\Block\Adminhtml\Report\Filter\Form;

use Ewave\Club\Api\ClubRepositoryInterface;
use Ewave\Club\Api\Data\ClubInterface;
use Ewave\Event\Api\Data\EventInterface;
use Ewave\Event\Api\EventRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Event
 * @package Ewave\EventGenerateReport\Block\Adminhtml\Report\Filter\Form
 */
class Event extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var ClubRepositoryInterface
     */
    private $clubRepository;

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        ClubRepositoryInterface $clubRepository,
        EventRepositoryInterface $eventRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = [])
    {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->clubRepository = $clubRepository;
        $this->eventRepository = $eventRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }


    /**
     * Add fieldset with general report fields
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $actionUrl = $this->getUrl('*/*/event');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'filter_form',
                    'action' => $actionUrl,
                    'method' => 'get',
                ],
            ]
        );

        $htmlIdPrefix = 'event_report_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Filter')]);


        $clubOptions = $this->getClubsOptions();

        $fieldset->addField(
            'club_id',
            'select',
            [
                'name' => 'club_id',
                'options' => $clubOptions,
                'label' => __('Clubs'),
                'onchange' => 'initEventOptions(this.value)'
            ]
        );

        $fieldset->addField(
            'event_id',
            'select',
            [
                'name' => 'event_id',
                'options' => $this->getEventsOptions(key($clubOptions)),
                'label' => __('Events'),
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return array
     */
    public function getClubsOptions()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ClubInterface::IS_ACTIVE, true)
            ->create();
        $clubs = $this->clubRepository->getList($searchCriteria)->getItems();

        $options = [];
        foreach ($clubs as $club) {
            $options[$club->getId()] = $club->getName();
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getEventsOptions($clubId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $events = $this->eventRepository->getList($searchCriteria)->getItems();

        $options = [];
        foreach ($events as $event) {
            if ((int)$event->getClubId() !== $clubId) {
                continue;
            }
            $options[$event->getId()] = $event->getName();
        }

        return $options;
    }

}
