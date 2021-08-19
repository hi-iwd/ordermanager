<?php

namespace IWD\OrderManager\Plugin\Framework\View\Element\UiComponent\DataProvider;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as MagentoDataProvider;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

/**
 * Class DataProvider
 * @package IWD\OrderManager\Plugin\Framework\View\Element\UiComponent\DataProvider
 */
class DataProvider
{
    /**
     * @var MagentoDataProvider
     */
    private $subject;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $pricingHelper;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $totals = [
        'defaultTotals' => [
            'total'    => ['order' => 9999, 'page' => 999, 'label' => 'Total'],
            'subtotal' => ['order' => 9999, 'page' => 999, 'label' => 'Subtotal'],
        ],
        'additionalTotals' => [
            'tax'      => ['order' => 9999, 'page' => 999, 'label' => 'Tax'],
            'invoiced' => ['order' => 9999, 'page' => 999, 'label' => 'Invoiced'],
            'shipped'  => ['order' => 9999, 'page' => 999, 'label' => 'Shipping'],
            'refunded' => ['order' => 9999, 'page' => 999, 'label' => 'Refunds'],
            'discount' => ['order' => 9999, 'page' => 999, 'label' => 'Coupons']
        ]
    ];

    /**
     * DataProvider constructor.
     * @param PricingHelper $pricingHelper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        PricingHelper $pricingHelper,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->pricingHelper = $pricingHelper;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    private function isGridTotalsEnabled()
    {
        return (bool)$this->scopeConfig->getValue('iwdordermanager/order_grid/order_grid_enable');
    }

    /**
     * @return bool
     */
    private function isOrderGridDataSource()
    {
        return $this->subject->getName() == 'sales_order_grid_data_source';
    }

    /**
     * @param MagentoDataProvider $subject
     * @param Filter $filter
     */
    public function beforeAddFilter(MagentoDataProvider $subject, Filter $filter)
    {
        $field = $filter->getField();
        $field = (strpos($field, 'main_table') === false) ? 'main_table.' . $field : $field;
        $filter->setField($field);
    }

    /**
     * @param MagentoDataProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetData(MagentoDataProvider $subject, $result)
    {
        $this->subject = $subject;

        if ($this->isGridTotalsEnabled() && $this->isOrderGridDataSource()) {
            $result['iwdTotals'] = $this->getTotals();
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getTotals()
    {
        $this->prepareTotalOptions();
        $this->prepareTotals();

        return $this->totals;
    }

    /**
     * @return void
     */
    private function prepareTotalOptions()
    {
        $searchResult = $this->subject->getSearchResult();
        $searchCriteria = $this->subject->getSearchCriteria();

        $pageSize = $searchCriteria->getPageSize();
        $getCurPage = $searchCriteria->getCurrentPage();
        $to = $pageSize * $getCurPage;
        $size = $searchResult->getTotalCount();

        $this->totals['options'] = [
            'pageFrom' => $pageSize * ($getCurPage - 1) + 1,
            'pageTo' => ($to > $size) ? $size : $to,
            'ordersCount' => $size
        ];
    }

    /**
     * @return void
     */
    private function prepareTotals()
    {
        foreach ($this->totals['defaultTotals'] as $key => $val) {
            $this->totals['defaultTotals'][$key]['order']
                = $this->currencyFormat($this->totals['defaultTotals'][$key]['order']);
            $this->totals['defaultTotals'][$key]['page']
                = $this->currencyFormat($this->totals['defaultTotals'][$key]['page']);
        }

        foreach ($this->totals['additionalTotals'] as $key => $val) {
            $this->totals['additionalTotals'][$key]['page']
                = $this->currencyFormat($this->totals['additionalTotals'][$key]['page']);
            $this->totals['additionalTotals'][$key]['order']
                = $this->currencyFormat($this->totals['additionalTotals'][$key]['order']);
        }
    }

    /**
     * @param $amount
     * @return string
     */
    private function currencyFormat($amount)
    {
        return $this->pricingHelper->currency($amount, true, false);
    }
}
