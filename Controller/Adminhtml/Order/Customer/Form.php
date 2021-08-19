<?php

namespace IWD\OrderManager\Controller\Adminhtml\Order\Customer;

use IWD\OrderManager\Controller\Adminhtml\Order\AbstractAction;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Form
 * @package IWD\OrderManager\Controller\Adminhtml\Order\Customer
 */
class Form extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'IWD_OrderManager::iwdordermanager_customer';

    /**
     * @return string
     * @throws \Exception
     */
    public function getResultHtml()
    {
        $resultPage = $this->resultPageFactory->create();

        $customerFormContainer = $resultPage->getLayout()
            ->getBlock('iwdordermamager_order_customer_form');
        if (empty($customerFormContainer)) {
            throw new LocalizedException(__('Can not load block'));
        }

        $order = $this->getOrder();
        $customerFormContainer->setOrder($order);

        return $customerFormContainer->toHtml();
    }
}
