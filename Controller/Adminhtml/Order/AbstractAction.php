<?php

namespace IWD\OrderManager\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class AbstractAction
 * @package IWD\OrderManager\Controller\Adminhtml\Order
 */
abstract class AbstractAction extends Action
{
    /**
     * @var string[]
     */
    private $response;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;


    public $scopeConfig;

    /**
     * AbstractAction constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $result = $this->getResultHtml();
            if (!is_array($result)) {
                if (is_string($result) || get_class($result) == 'Magento\Framework\Phrase') {
                    $result = ['result' => $result];
                } else {
                    throw new LocalizedException(__('Disallowed result type'));
                }
            }
            $disallowedParams = ['allowed', 'status', 'error'];
            foreach ($result as $key => $param) {
                if (!in_array($key, $disallowedParams)) {
                    $this->response[$key] = $param;
                }
            }
            $this->response['allowed'] = 1;
            $this->response['status'] = true;
        } catch (\Exception $e) {
            $this->response = ['status' => false, 'error' => $e->getMessage()];
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($this->response);
    }

    /**
     * @return string|string[]
     */
    abstract public function getResultHtml();

    /**
     * Return order id from params
     * @return int
     * @throws \Exception
     */
    public function getOrderId()
    {
        $id = $this->getRequest()->getParam('order_id', null);
        if (empty($id)) {
            throw new LocalizedException(__('Empty param id'));
        }
        return $id;
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws \Exception
     */
    public function getOrder()
    {
        $orderId = $this->getOrderId();
        return $this->orderRepository->get($orderId);
    }
}
