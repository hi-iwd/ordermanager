<?php

namespace IWD\OrderManager\Block\Adminhtml\Shipment\View;

use Magento\Backend\Block\Widget\Container;

/**
 * Class Toolbar
 * @package IWD\OrderManager\Block\Adminhtml\Shipment\View
 */
class Toolbar extends Container
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addButton(
            'iwd_shipment_delete',
            [
                'label' => 'Delete',
                'class' => 'delete iwd-upgrade-to-pro'
            ]
        );
    }
}
