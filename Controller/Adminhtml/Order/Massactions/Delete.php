<?php

namespace IWD\OrderManager\Controller\Adminhtml\Order\Massactions;

use IWD\OrderManager\Model\Order\Order;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class Delete
 * @package IWD\OrderManager\Controller\Adminhtml\Order\Massactions
 */
class Delete extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'IWD_OrderManager::iwdordermanager_delete';

    /**
     * @var Order
     */
    private $order;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param Order $order
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Order $order
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->order = $order;
    }

    /**
     * {@inheritdoc}
     */
    protected function massAction(AbstractCollection $collection)
    {
        $countDeletedOrder = 0;
        foreach ($collection->getItems() as $item) {
            $order = clone $this->order->load($item->getId());
            if ($order->isAllowDeleteOrder()) {
                $order->delete();
                $this->removeRmaByOrderId($item->getId());
                $countDeletedOrder++;
            }
        }

        $countNonDeletedOrder = count($collection->getItems()) - $countDeletedOrder;

        if ($countNonDeletedOrder && $countDeletedOrder) {
            $this->messageManager->addErrorMessage(
                __('Order %1 could not be deleted as deletion of orders is not permitted. You may enable this option in the Order Manager settings.', $countNonDeletedOrder)
            );
        } elseif ($countNonDeletedOrder) {
            $this->messageManager->addErrorMessage(
                __('Order could not be deleted as deletion of orders is not permitted. You may enable this option in the Order Manager settings.')
            );
        }

        if ($countDeletedOrder) {
            $this->messageManager->addSuccessMessage(
                __('You have successfully deleted %1 order(s).', $countDeletedOrder)
            );
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }

    public function removeRmaByOrderId($orderId){
        if (class_exists(\Magento\Rma\Model\Rma::class)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $rmaModel = clone $objectManager->create('\Magento\Rma\Model\RmaFactory')->create();
            $rmaCollection = $rmaModel->getCollection()->addFieldToSelect('*')->addFieldToFilter('order_id', $orderId);
            foreach($rmaCollection as $key => $item) {
                $rmaModel->load($item->getEntityId())->delete();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getComponentRefererUrl()
    {
        return 'sales/order/index';
    }
}
