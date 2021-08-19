<?php

namespace IWD\OrderManager\Controller\Adminhtml\Order\Info;

use IWD\OrderManager\Model\Order\OrderData;
use IWD\OrderManager\Controller\Adminhtml\Order\AbstractAction;
use IWD\OrderManager\Model\Log\Logger;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Update
 * @package IWD\OrderManager\Controller\Adminhtml\Order\Info
 */
class Update extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'IWD_OrderManager::iwdordermanager_info';

    public $scopeConfig;

    public $messageManager;

    /**
     * @var OrderData
     */
    private $orderData;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderData $orderData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        OrderData $orderData
    ) {
        parent::__construct($context, $resultPageFactory, $orderRepository,$scopeConfig);
        $this->orderData = $orderData;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $context->getMessageManager();
    }

    /**
     * @return null|string
     * @throws \Exception
     */
    public function getResultHtml()
    {
        $orderInfo = $this->getRequest()->getParam('order_info', []);
        $order = $this->getOrder();

        $status = explode(",", $this->scopeConfig->getValue('iwdordermanager/general/order_statuses'));

        if(in_array($order->getStatus(),$status)){
            $order->setParams($orderInfo);
            Logger::getInstance()->addMessageForLevel('order_info', 'Order information was changed');
            $order->updateStatus()->save();
        }else{
            $this->messageManager->addError( __('The status of these orders cannot be changed. You may enable this option in the Order Manager settings.') );
        }


        return ['result' => 'reload'];
    }

    /**
     * @return OrderData
     * @throws \Exception
     */
    public function getOrder()
    {
        $orderId = $this->getOrderId();
        return $this->orderData->load($orderId);
    }
}
