<?php

namespace IWD\OrderManager\Block\Adminhtml\Creditmemo\View;

use Magento\Backend\Block\Widget\Container;

/**
 * Class Toolbar
 * @package IWD\OrderManager\Block\Adminhtml\Creditmemo\View
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
            'iwd_creditmemo_delete',
            [
                'label'   => 'Delete',
                'class'   => 'delete iwd-upgrade-to-pro',
            ]
        );
    }
}
