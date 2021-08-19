<?php

namespace IWD\OrderManager\Controller\Adminhtml\Order\Customer;

use IWD\OrderManager\Model\Order\OrderData;
use IWD\OrderManager\Helper\Data;
use IWD\OrderManager\Controller\Adminhtml\Order\AbstractAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Customer;
use IWD\OrderManager\Model\Log\Logger;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Update
 * @package IWD\OrderManager\Controller\Adminhtml\Order\Customer
 */
class Update extends AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'IWD_OrderManager::iwdordermanager_customer';

    /**
     * @var OrderData
     */
    private $orderData;

    /**
     * @var Customer
     */
    private $customer;

    public $scopeConfig;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderData $orderData
     * @param Customer $customer
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        OrderData $orderData,
        Customer $customer
    ) {
        parent::__construct($context, $resultPageFactory, $orderRepository,$scopeConfig);
        $this->orderData = $orderData;
        $this->customer = $customer;
    }

    /**
     * @return null|string
     * @throws \Exception
     */
    public function getResultHtml()
    {
        $this->updateOrderCustomerInfo();
        $this->updateCustomerInfo();

        return ['result' => 'reload'];
    }

    /**
     * @return void
     */
    private function updateOrderCustomerInfo()
    {
        $orderInfo = $this->getRequest()->getParam('customer_info', []);
        $order = $this->getOrder();

        $order->setParams($orderInfo);

        Logger::getInstance()->addMessageForLevel(
            'customer_info',
            'Order customer information was changed'
        );

        $order->updateCustomerGroups()
            ->updateCustomerEmail()
            ->updatePrefix()
            ->updateFirstName()
            ->updateMiddleName()
            ->updateLastnameName()
            ->updateSuffix()
            ->updateGender()
            ->updateTaxvat()
            ->updateDateOfBirth()
            ->updateCustomerId();

        $order->save();
    }

    /**
     * @return void
     */
    private function updateCustomerInfo()
    {
        $applyForCustomer = $this->getRequest()->getParam('apply_for_customer', false);
        if (empty($applyForCustomer)) {
            return;
        }

        $customerInfo = $this->getRequest()->getParam('customer_info', []);
        if (!isset($customerInfo['customer_id']) || empty($customerInfo['customer_id'])) {
            return;
        }

        $customerId = $customerInfo['customer_id'];
        $websiteId = $this->getOrder()->getStore()->getWebsiteId();

        $this->customer->setWebsiteId($websiteId);

        $customer = $this->customer->load($customerId);
        if (!$customer->getId()) {
            return;
        }

        $params = [
            'customer_group_id'   => 'group_id',
            'customer_dob'        => 'dob',
            'customer_email'      => 'email',
            'customer_firstname'  => 'firstname',
            'customer_lastname'   => 'lastname',
            'customer_middlename' => 'middlename',
            'customer_prefix'     => 'prefix',
            'customer_suffix'     => 'suffix',
            'customer_taxvat'     => 'taxvat',
            'customer_gender'     => 'gender',
        ];

        foreach ($params as $id => $param) {
            if (isset($customerInfo[$id])) {
                $customer->setData($param, $customerInfo[$id]);
            }
        }

        Logger::getInstance()->addMessage(
            'Customer information was updated based on order customer information.'
        );
        $customer->save();
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
