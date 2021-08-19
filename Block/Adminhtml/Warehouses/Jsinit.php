<?php

namespace IWD\OrderManager\Block\Adminhtml\Warehouses;

use Magento\Backend\Block\Template;

class Jsinit extends Template
{
    /**
     * @return string
     */
    public function jsonParamsWarehouses()
    {
        $data = [
            'urlForm' => $this->_urlBuilder->getUrl('iwdordermanager/warehouses/stocks_data'),
            'urlUpdate' => $this->_urlBuilder->getUrl('iwdordermanager/warehouses/stocks_update'),
        ];

        return json_encode($data);
    }
}
