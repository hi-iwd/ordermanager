<?php

namespace IWD\OrderManager\Controller\Adminhtml\Order\Info;

use IWD\OrderManager\Controller\Adminhtml\Order\AbstractAction;
use Magento\Framework\Exception\LocalizedException;

class Form extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'IWD_OrderManager::iwdordermanager_info';

    /**
     * @return string
     * @throws \Exception
     */
    public function getResultHtml()
    {
        $resultPage = $this->resultPageFactory->create();

        /** @var \IWD\OrderManager\Block\Adminhtml\Order\Info\Form $infoFormContainer */
        $infoFormContainer = $resultPage->getLayout()->getBlock('iwdordermamager_order_info_form');
        if (empty($infoFormContainer)) {
            throw new LocalizedException(__('Can not load block'));
        }

        $order = $this->getOrder();
        $infoFormContainer->setOrder($order);

        return $infoFormContainer->toHtml();
    }
}
