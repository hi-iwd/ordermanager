<?php

namespace IWD\OrderManager\Model\Inventory;

use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\InventoryReservationsApi\Model\ReservationInterface;
use Magento\Catalog\Model\ProductFactory;
use Psr\Log\LoggerInterface as Logger;

class Reservation
{
    /**
     * @var Validation
     */
    private $inventoryValidation;

    /**
     * @var StockRegistryProviderInterface
     */
    private $stockRegistryProvider;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Reservation constructor.
     * @param Validation $inventoryValidation
     * @param StockRegistryProviderInterface $stockRegistryProvider
     * @param ResourceConnection $resource
     * @param ProductFactory $productFactory
     * @param Logger $logger
     */
    public function __construct(
        Validation $inventoryValidation,
        StockRegistryProviderInterface $stockRegistryProvider,
        ResourceConnection $resource,
        ProductFactory $productFactory,
        Logger $logger
    ) {
        $this->inventoryValidation = $inventoryValidation;
        $this->stockRegistryProvider = $stockRegistryProvider;
        $this->resource = $resource;
        $this->productFactory = $productFactory;
        $this->logger = $logger;
    }

    /**
     * @param $item
     * @return true
     */
    public function execute($item)
    {
        $product = $this->getProductById($item->getProductId());

        if (empty($product) || empty($item) || !$this->inventoryValidation->isAllowedInventorySalesApi()) {
            return true;
        }

        try {
            $connection = $this->resource->getConnection();
            $reservationTable = $this->resource->getTableName('inventory_reservation');

            $select = $connection->select()
                ->from(
                    ['c' => $reservationTable],
                    ['*']
                )
                ->where(
                    "c.sku = ? :sku"
                )
                ->where(
                    "c.metadata LIKE ? :metadata"
                );

            $bind = ['sku' => $product->getSku(), 'metadata' => '%' . $item->getIncrementId() . '%'];

            $conditions = [
                ReservationInterface::METADATA . ' LIKE ? ' => '%'. $item->getIncrementId() .'%',
                ReservationInterface::SKU . ' = ? ' => $product->getSku()
            ];

            if($item->getType() == 'reduce' && $item->getQtyShipped()) {
                $this->restoreQty($item->getProductId(), $item->getQtyShipped(), $item->getStoreId());
            }

            $itemDB = $connection->fetchRow($select, $bind);

            if(!empty($itemDB)) {
                $itemDBQty = abs((float)$itemDB['quantity']);

                if($item->getType() == 'increase') {
                    $newQty = $itemDBQty + $item->getQty();
                } else {
                    $newQty = $itemDBQty - $item->getQty();
                }

                $conditions[ReservationInterface::RESERVATION_ID . ' = ? '] = $itemDB['reservation_id'];
                $bind = ['quantity' => -$newQty];

                $connection->update($reservationTable, $bind, $conditions);
            }
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
        }

        return true;
    }

    /**
     * @param $productId
     * @param $qty
     * @param $storeId
     * @return void
     */
    public function restoreQty ($productId, $qty, $storeId) {
        try {
            $stockItem = $this->stockRegistryProvider->getStockItem($productId, $storeId);
            $stockItem->setQty($stockItem->getQty() + (float)$qty);
            $stockItem->save();
        } catch (\Exception $e) {
            // do nothing
            $this->logger->warning($e->getMessage());
        }
    }

    public function removeAllReservatationByOrder($order) {
        $this->removeAllReservationByOrderIncrement($order);
    }

    public function removeAllReservationByOrderIncrement($order, $all = false) {
        try {
            $connection = $this->resource->getConnection();
            $reservationTable = $this->resource->getTableName('inventory_reservation');
            $select = $connection->select()
                ->from(
                    ['c' => $reservationTable],
                    ['*']
                )
                ->where(
                    "c.metadata LIKE ? :metadata"
                );
            $bind = ['metadata' => '%' . $order->getIncrementId() . '%'];

            $itemsDB = $connection->fetchAll($select, $bind);

            if(!empty($itemsDB)) {
                foreach ($itemsDB as $itemDB) {
                    $metadata = json_decode($itemDB['metadata']);
                    $conditions[ReservationInterface::RESERVATION_ID . ' = ? '] = $itemDB['reservation_id'];

                    if($all) {
                        $connection->delete($reservationTable, $conditions);
                    } else {
                        if($metadata->event_type == 'shipment_created') {
                            $connection->delete($reservationTable, $conditions);
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            // do nothing
            $this->logger->warning($e->getMessage());
        }
    }

    private function getProductById($id) {
        try {
            $product = $this->productFactory->create()->load($id);
        } catch (\Exception $e) {
            $product = false;
        }

        return $product;
    }
}
