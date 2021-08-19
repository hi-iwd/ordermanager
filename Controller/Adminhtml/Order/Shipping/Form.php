<?php

namespace IWD\OrderManager\Controller\Adminhtml\Order\Shipping;

use IWD\OrderManager\Model\Quote\Quote;
use IWD\OrderManager\Controller\Adminhtml\Order\AbstractAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Form
 * @package IWD\OrderManager\Controller\Adminhtml\Order\Shipping
 */
class Form extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'IWD_OrderManager::iwdordermanager_shipping';

    /**
     * @var Quote
     */
    private $quote;

    public $scopeConfig;

    /**
     * Form constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param Quote $quote
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Quote $quote
    ) {
        parent::__construct($context, $resultPageFactory, $orderRepository,$scopeConfig);
        $this->quote = $quote;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getResultHtml()
    {
        /** @var \IWD\OrderManager\Block\Adminhtml\Order\Shipping\Form $shippingFormContainer */
        $shippingFormContainer = $this->resultPageFactory->create()
            ->getLayout()
            ->getBlock('iwdordermamager_order_shipping_form');
        if (empty($shippingFormContainer)) {
            throw new LocalizedException(__('Can not load block'));
        }

        $quote = $this->getQuote();
        $order = $this->getOrder();
        $shippingFormContainer->setQuote($quote);
        $shippingFormContainer->setOrder($order);

        return $shippingFormContainer->toHtml();
    }

    /**
     * @return Quote
     * @throws \Exception
     */
    private function getQuote()
    {
        $quoteId = $this->getOrder()->getQuoteId();
        return $this->quote->load($quoteId);
    }
}
