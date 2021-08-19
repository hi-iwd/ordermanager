<?php

namespace IWD\OrderManager\Controller\Adminhtml\Order\Address;

use IWD\OrderManager\Model\Order\Address;
use IWD\OrderManager\Controller\Adminhtml\Order\AbstractAction;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

/**
 * Class Form
 * @package IWD\OrderManager\Controller\Adminhtml\Order\Address
 */
class Form extends AbstractAction
{
    /**
     * @var \IWD\OrderManager\Model\Order\Address
     */
    private $address;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    public $scopeConfig;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param OrderRepositoryInterface $orderRepository,
     * @param Address $address
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Address $address,
        Registry $coreRegistry
    ) {
        parent::__construct($context, $resultPageFactory, $orderRepository,$scopeConfig);
        $this->address = $address;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultHtml()
    {
        $this->prepareAddress();

        $resultPage = $this->resultPageFactory->create();

        /**
         * @var \IWD\OrderManager\Block\Adminhtml\Order\Address\Form $addressFormContainer
         */
        $addressFormContainer = $resultPage->getLayout()->getBlock('iwdordermamager_order_address_form_container');
        if (empty($addressFormContainer)) {
            throw new LocalizedException(__('Can not load block'));
        }

        $addressType = $this->getAddressType();

        return $addressFormContainer->setAddressType($addressType)->toHtml();
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getAddressType()
    {
        $addressType = $this->getRequest()->getParam('address_type', null);
        if (empty($addressType)) {
            throw new LocalizedException(__('Address type is empty'));
        }
        return $addressType;
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function prepareAddress()
    {
        $addressId = $this->getRequest()->getParam('address_id', 0);
        $address = $this->address->loadAddress($addressId);
        $this->coreRegistry->register('order_address', $address);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return
            $this->_authorization->isAllowed('IWD_OrderManager::iwdordermanager_address_billing') ||
            $this->_authorization->isAllowed('IWD_OrderManager::iwdordermanager_address_shipping');
    }
}
