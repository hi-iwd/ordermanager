<?php

namespace IWD\OrderManager\Block\Adminhtml\Shipment\View;

use IWD\OrderManager\Block\Adminhtml\Order\View\Jsinit as OrderJsinit;

/**
 * Class Jsinit
 * @package IWD\OrderManager\Block\Adminhtml\Shipment\View
 */
class Jsinit extends OrderJsinit
{
    /**
     * @return string
     */
    public function jsonParamsShipmentInfo()
    {
        $data = [
            'urlForm' => $this->_urlBuilder->getUrl('iwdordermanager/shipment_info/form'),
            'urlUpdate' => '#',
            'disallowed' => []
        ];

        return json_encode($data);
    }
}
