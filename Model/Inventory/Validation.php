<?php

namespace IWD\OrderManager\Model\Inventory;

use Magento\Framework\Module\Manager;

class Validation
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * Validation constructor.
     * @param Manager $moduleManager
     */
    public function __construct(
        Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    public function isAllowedInventorySalesApi () {
        if($this->moduleManager->isEnabled('Magento_InventorySalesApi') && interface_exists(\Magento\InventorySalesApi\Api\GetProductSalableQtyInterface::class)) {
            return true;
        }

        return false;
    }

}
