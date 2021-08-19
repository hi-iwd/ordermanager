<?php

namespace IWD\OrderManager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 * @package IWD\OrderManager\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Is Allow
     */
    const IS_ALLOW = 'isAllow';

    /**
     * Store
     */
    const STORE = 'store';

    /**
     * Details
     */
    const DETAILS = 'details';

    /**
     * XPath: extension enable
     */
    const ENABLED = 'iwdordermanager/general/enable';

    /**
     * XPath: multi inventory enable
     */
    const MULTISTOCK_ENABLED = 'iwdordermanager/multi_inventory/enable';

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Framework\Math\CalculatorFactory
     */
    private $calculatorFactory;

    /**
     * Calculator instances for delta rounding of prices
     * @var float[]
     */
    private $calculators = [];

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Math\CalculatorFactory $calculatorFactory
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Math\CalculatorFactory $calculatorFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        parent::__construct($context);
        $this->calculatorFactory = $calculatorFactory;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return string
     */
    public function getMagentoEdition()
    {
        $edition = $this->productMetadata->getEdition();
        return $edition;
    }

    /**
     * @return int|bool
     */
    public function isMultiStockEnabled()
    {
        return $this->scopeConfig->getValue(self::MULTISTOCK_ENABLED);
    }

    /**
     * @return mixed
     */
    public function isExtensionEnabled()
    {
        return $this->scopeConfig->getValue(self::ENABLED);
    }

    /**
     * Round price considering delta
     *
     * @param float $price
     * @param string $type
     * @param bool $negative Indicates if we perform addition (true) or subtraction (false) of rounded value
     * @return float
     */
    public function roundPrice($price, $type = 'regular', $negative = false)
    {
        if ($price) {
            if (!isset($this->calculators[$type])) {
                $this->calculators[$type] = $this->calculatorFactory->create(['scope' => $this->getStore()]);
            }
            $price = $this->calculators[$type]->deltaRound($price, $negative);
        }
        return $price;
    }
}
