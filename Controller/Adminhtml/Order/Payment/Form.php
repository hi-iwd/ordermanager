<?php

namespace IWD\OrderManager\Controller\Adminhtml\Order\Payment;

use IWD\OrderManager\Model\Quote\Quote;
use IWD\OrderManager\Controller\Adminhtml\Order\AbstractAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Form
 * @package IWD\OrderManager\Controller\Adminhtml\Order\Payment
 */
class Form extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'IWD_OrderManager::iwdordermanager_payment';

    /**
     * @var Quote
     */
    private $quote;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    private $sessionQuote;

    /**
     * @var \IWD\OrderManager\Model\Rewrite\Session\Quote
     */
    private $savedSessionQuote;

    public $scopeConfig;

    /**
     * Form constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param Quote $quote
     * @param \IWD\OrderManager\Model\Rewrite\Session\Quote $sessionQuote
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Quote $quote,
        \IWD\OrderManager\Model\Rewrite\Session\Quote $sessionQuote
    ) {
        parent::__construct($context, $resultPageFactory, $orderRepository,$scopeConfig);
        $this->quote = $quote;
        $this->sessionQuote = $sessionQuote;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getResultHtml()
    {
        $this->sessionQuote->getQuote();
        $this->saveSessionQuote();
        $this->loadNewSessionQuote();

        $formHtml = $this->getPaymentEditForm();

        $this->restoreSessionQuote();

        return $formHtml;
    }

    /**
     * @return void
     */
    private function saveSessionQuote()
    {
        $this->savedSessionQuote = $this->sessionQuote->getQuote();
    }

    /**
     * @return void
     */
    private function loadNewSessionQuote()
    {
        $order = $this->getOrder();
        $quoteId = $order->getQuoteId();
        $storeId = $order->getStoreId();
        $currencyId = $order->getOrderCurrencyCode();

        $this->sessionQuote->clearQuoteParams();

        $this->sessionQuote->setQuoteId($quoteId);
        $this->sessionQuote->setStoreId($storeId);
        $this->sessionQuote->setCurrencyId($currencyId);
        $this->sessionQuote->getQuote();
    }

    /**
     * @return void
     */
    private function restoreSessionQuote()
    {
        $quoteId = $this->savedSessionQuote->getQuoteId();
        $storeId = $this->savedSessionQuote->getStoreId();
        $currencyId = $this->savedSessionQuote->getCurrencyId();

        $this->sessionQuote->clearQuoteParams();

        $this->sessionQuote->setQuoteId($quoteId);
        $this->sessionQuote->setStoreId($storeId);
        $this->sessionQuote->setCurrencyId($currencyId);
        $this->sessionQuote->getQuote();
    }

    /**
     * @return \Magento\Backend\Model\Session\Quote
     */
    protected function _getSession()
    {
        return $this->sessionQuote;
    }

    /**
     * @return string
     */
    private function getPaymentEditForm()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle('sales_order_create_load_block_billing_method');
        $paymentHtml = $resultPage->getLayout()->renderElement('content');

        return '<div class="iwd-locked-feature"><form id="order-billing_method">' . $paymentHtml .
            '</form><div class="iwd-lock-pro"></div>
            <div class="iwd-unlock-pro iwd-upgrade-to-pro"><i class="fa fa-lock"></i>Unlock Pro</div>
            </div>';
    }
}
