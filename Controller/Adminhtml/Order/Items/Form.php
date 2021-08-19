<?php

namespace IWD\OrderManager\Controller\Adminhtml\Order\Items;

use IWD\OrderManager\Controller\Adminhtml\Order\AbstractAction;
use Magento\Framework\Exception\LocalizedException;

class Form extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    public function getResultHtml()
    {
        $formContainer = $this->resultPageFactory->create()->getLayout()
            ->getBlock('iwdordermamager_order_items_form_container');

        if (empty($formContainer)) {
            throw new LocalizedException(__('Can not load block'));
        }

        $order = $this->getOrder();

        $formContainer->setOrder($order);

        return $formContainer->toHtml();
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return
            $this->_authorization->isAllowed('IWD_OrderManager::iwdordermanager_items_edit') ||
            $this->_authorization->isAllowed('IWD_OrderManager::iwdordermanager_items_delete') ||
            $this->_authorization->isAllowed('IWD_OrderManager::iwdordermanager_items_add');
    }
}
