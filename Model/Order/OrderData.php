<?php

namespace IWD\OrderManager\Model\Order;

use IWD\OrderManager\Model\Log\Logger;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class OrderData
 * @package IWD\OrderManager\Model\Order
 */
class OrderData extends Order
{
    /**
     * @var string[]
     */
    private $params = [];

    /**
     * @param string[] $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $index
     * @return null|int|string
     */
    public function getParam($index)
    {
        $orderInfo = $this->getParams();
        if (isset($orderInfo[$index])) {
            return $orderInfo[$index];
        }
        return null;
    }

    /**
     * @param string $index
     * @param null $title
     * @param null $level
     * @return $this
     */
    private function updateOrderData($index, $title=null, $level=null)
    {
        $val = $this->getParam($index);

        if ($val != null) {
            $old = $this->getData($index);
            $this->setData($index, $val);
            $new = $this->getData($index);

            Logger::getInstance()->addChange($title, $old, $new, $level);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function updateStatus()
    {
        return $this->updateOrderData('status', 'Status', 'order_info');
    }

    /**
     * @return $this
     */
    public function updateCustomerGroups()
    {
        return $this->updateOrderData('customer_group_id', 'Group id', 'customer_info');
    }

    /**
     * @return $this
     */
    public function updateCustomerEmail()
    {
        return $this->updateOrderData('customer_email', 'Customer email', 'customer_info');
    }

    /**
     * @return $this
     */
    public function updatePrefix()
    {
        return $this->updateOrderData('customer_prefix', 'Prefix', 'customer_info');
    }

    /**
     * @return $this
     */
    public function updateFirstName()
    {
        return $this->updateOrderData('customer_firstname', 'First name', 'customer_info');
    }

    /**
     * @return $this
     */
    public function updateMiddleName()
    {
        return $this->updateOrderData('customer_middlename', 'Middle name', 'customer_info');
    }

    /**
     * @return $this
     */
    public function updateLastnameName()
    {
        return $this->updateOrderData('customer_lastname', 'Last name', 'customer_info');
    }

    /**
     * @return $this
     */
    public function updateSuffix()
    {
        return $this->updateOrderData('customer_suffix', 'Suffix', 'customer_info');
    }

    /**
     * @return $this
     */
    public function updateGender()
    {
        return $this->updateOrderData('customer_gender', 'Gender', 'customer_info');
    }

    /**
     * @return $this
     */
    public function updateTaxvat()
    {
        return $this->updateOrderData('customer_taxvat', 'Tax/VAT number', 'customer_info');
    }

    /**
     * @return $this
     */
    public function updateDateOfBirth()
    {
        $val = $this->getParam('customer_dob');

        if ($val != null) {
            $old = date('Y-m-d', strtotime($this->getData('customer_dob')));
            $this->setData('customer_dob', $val);
            $new = date('Y-m-d', strtotime($this->getData('customer_dob')));

            Logger::getInstance()->addChange('Day of birthday', $old, $new, 'customer_info');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function updateCustomerId()
    {
        $oldCustomerId = $this->getData('customer_id');

        $this->updateOrderData('customer_id');

        $newCustomerId = $this->getData('customer_id');

        if ($oldCustomerId != $newCustomerId) {
            $this->_eventManager->dispatch(
                'iwd_om_change_order_customer',
                [
                    'order_id' => $this->getEntityId(),
                    'customer_id' => $newCustomerId,
                    'old_customer_id' => $oldCustomerId
                ]
            );

            $this->updateRelatedCustomerInfo();

            $oldCustomer = $this->getCustomerRepository()->getById($oldCustomerId);
            $newCustomer = $this->getCustomerRepository()->getById($newCustomerId);
            $old = $oldCustomer->getFirstname() . ' ' . $oldCustomer->getLastname();
            $new = $newCustomer->getFirstname() . ' ' . $newCustomer->getLastname();
            Logger::getInstance()->addChange('Customer', $old, $new);
        }

        return $this;
    }

    /**
     * @return void
     */
    private function updateRelatedCustomerInfo()
    {
        //remove CustomerAddressId, because it's not correct info now and got an error for reorder
        $addresses = $this->getAddresses();
        foreach ($addresses as $address) {
            $address->setCustomerAddressId(null)->save();
        }
    }
}
