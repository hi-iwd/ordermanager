<?php

namespace IWD\OrderManager\Block\Adminhtml\Invoice\View;

use Magento\Backend\Block\Widget\Container;

/**
 * Class Toolbar
 * @package IWD\OrderManager\Block\Adminhtml\Invoice\View
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
            'iwd_invoice_delete',
            [
                'label'   => 'Delete',
                'class'   => 'delete iwd-upgrade-to-pro',
            ]
        );
    }
}
