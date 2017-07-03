<?php

// TODO temp class. remove it later

namespace Ewave\BannerStaging\Block\Adminhtml\Banner;
use Magento\Framework\View\Element\Template;

/**
 * Class Update
 */
class Update extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;
    /**
     * @var \Magento\Framework\Translate\InlineInterface
     */
    private $translateInline;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Update constructor.
     * @param Template\Context $context
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->layoutFactory = $layoutFactory;
        $this->translateInline = $translateInline;
        $this->registry = $registry;
        $this->objectManager = $objectManager;
    }

    public function _toHtml()
    {
        $bannerId = (int)$this->getRequest()->getParam('id');
        $model = $this->objectManager->create('Ewave\BannerStaging\Api\Data\BannerInterface');
        if ($bannerId) {
            $model->load($bannerId);
        }
        if (!$this->registry->registry('current_banner')) {
            $this->registry->register('current_banner', $model);
        }

        return '';
    }

    /**
     * Render HTML based on requested layout handle name
     *
     * @param string $handle
     * @return string
     */
    protected function _getHtmlByHandle($handle)
    {
        $layout = $this->layoutFactory->create();
        $layout->getUpdate()->load([$handle]);
        $layout->generateXml();
        $layout->generateElements();
        $output = $layout->getOutput();
        $this->translateInline->processResponseBody($output);
        return $output;
    }


}
