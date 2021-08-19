<?php

namespace IWD\OrderManager\Block\Adminhtml\Creditmemo\View;

use IWD\OrderManager\Block\Adminhtml\Order\View\Jsinit as OrderJsinit;

/**
 * Class Jsinit
 * @package IWD\OrderManager\Block\Adminhtml\Creditmemo\View
 */
class Jsinit extends OrderJsinit
{
    /**
     * @return string
     */
    public function jsonParamsCreditmemoInfo()
    {
        $data = [
            'urlForm' => $this->_urlBuilder->getUrl('iwdordermanager/creditmemo_info/form'),
            'urlUpdate' => '#',
            'disallowed' => []
        ];

        return json_encode($data);
    }
}
