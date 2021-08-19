<?php

namespace IWD\OrderManager\Block\Adminhtml\Warehouses\Source;

use \Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Grid
 * @package IWD\OrderManager\Block\Adminhtml\Warehouses\Source
 */
class Grid extends Container
{
    /**
     * Initialize object state with incoming parameters
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'IWD_OrderManager';
        $this->_controller = 'adminhtml_warehouses';
        $this->_headerText = __('Source');
        $this->_addButtonLabel = __('Add New Source');

        parent::_construct();
    }
}
