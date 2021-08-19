<?php

namespace IWD\OrderManager\Controller\Adminhtml\Shipment\Info;

use IWD\OrderManager\Controller\Adminhtml\Order\AbstractAction;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Form
 * @package IWD\OrderManager\Controller\Adminhtml\Shipment\Info
 */
class Form extends AbstractAction
{
    /**
     * @return string
     * @throws \Exception
     */
    public function getResultHtml()
    {
        $resultPage = $this->resultPageFactory->create();

        /** @var \IWD\OrderManager\Block\Adminhtml\Shipment\Info\Form $infoFormContainer */
        $infoFormContainer = $resultPage->getLayout()->getBlock('iwdordermamager_shipment_info_form');
        if (empty($infoFormContainer)) {
            throw new LocalizedException(__('Can not load block'));
        }

        $shipmentId = $this->getShipmentId();
        $infoFormContainer->setShipmentId($shipmentId);

        return $infoFormContainer->toHtml();
    }

    /**
     * @return int
     * @throws \Exception
     */
    private function getShipmentId()
    {
        $id = $this->getRequest()->getParam('shipment_id', null);
        if (empty($id)) {
            throw new LocalizedException(__('Empty param shipment id'));
        }
        return $id;
    }
}
