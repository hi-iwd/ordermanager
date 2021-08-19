<?php

namespace IWD\OrderManager\Controller\Adminhtml\Order\Address;

use IWD\OrderManager\Model\Order\Address;
use IWD\OrderManager\Controller\Adminhtml\Order\AbstractAction;
use IWD\OrderManager\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use IWD\OrderManager\Model\Log\Logger;
use Magento\Sales\Api\OrderRepositoryInterface;

class Update extends AbstractAction
{
    /**
     * @var \IWD\OrderManager\Model\Order\Address
     */
    private $address;

    public $scopeConfig;

    /**
     * Update constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param OrderRepositoryInterface $orderRepository,
     * @param Address $address
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Address $address
    ) {
        parent::__construct($context, $resultPageFactory, $orderRepository,$scopeConfig);
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getResultHtml()
    {
        $addressId = $this->getAddressId();
        $addressData = $this->getAddressData();

        $this->address->loadAddress($addressId);
        $this->address->updateAddress($addressData);
        $this->updateUserAddress($addressData);

        return ['result' => 'reload'];
    }

    /**
     * @param string[] $addressData
     * @return void
     */
    private function updateUserAddress($addressData)
    {
        $applyForCustomer = $this->getRequest()->getParam('apply_for_customer', false);

        if (!empty($applyForCustomer)) {
            $this->address->updateCustomerAddress($addressData);
            Logger::getInstance()->addMessage(
                'Customer address information was updated based on order address information.'
            );
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getAddressId()
    {
        $id = $this->getRequest()->getParam('address_id', null);
        if (empty($id)) {
            throw new LocalizedException(__('Empty param address_id'));
        }

        return $id;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getAddressData()
    {
        $data = $this->getRequest()->getParams();

        if (isset($data['billing_address'])) {
            return $data['billing_address'];
        }

        if (isset($data['shipping_address'])) {
            return $data['shipping_address'];
        }

        throw new LocalizedException(__('Have not address data information'));
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('IWD_OrderManager::iwdordermanager_address_billing')
            || $this->_authorization->isAllowed('IWD_OrderManager::iwdordermanager_address_shipping');
    }
}
