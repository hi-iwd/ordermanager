<?php

namespace IWD\OrderManager\Block\Adminhtml;

use Magento\Backend\Block\Template;
use IWD\OrderManager\Helper\Data;

/**
 * Class Jsinit
 * @package IWD\OrderManager\Block\Adminhtml
 */
class Jsinit extends Template
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @param Template\Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @return bool|int
     */
    public function isMultiStockEnabled()
    {
        return $this->helper->isMultiStockEnabled();
    }
}
