<?php
/**
 * @category   Sales
 * @package    Quafzi_AutoShippingStatus
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Thomas Birke <tbirke@netextreme.de>
 */
class Quafzi_AutoShippingStatus_Model_Observer
{
    public function sales_order_shipment_save_after($event)
    {
        $order = $event->getShipment()->getOrder();
        if ((bool)(int)Mage::getStoreConfig('shipping/option/switchToPartial')) {
            $sourcePath = 'shipping/option/statusBeforeSwitchToPartial';
            $sourceStatuses = explode(',', Mage::getStoreConfig($sourcePath));
            $targetPath = 'shipping/option/statusAfterSwitchToPartial';
            $shippedStatus = Mage::getStoreConfig($targetPath);
            if (true == $order->canShip()
                && in_array($order->getStatus(), $sourceStatuses)
            ) {
                $order->setStatus($shippedStatus)
                    ->save();
            }
        }
        if ((bool)(int)Mage::getStoreConfig('shipping/option/switchToShipped')) {
            $sourcePath = 'shipping/option/statusBeforeSwitchToShipped';
            $sourceStatuses = explode(',', Mage::getStoreConfig($sourcePath));
            $targetPath = 'shipping/option/statusAfterSwitchToShipped';
            $shippedStatus = Mage::getStoreConfig($targetPath);
            if (false == $order->canShip()
                && in_array($order->getStatus(), $sourceStatuses)
            ) {
                $order->setStatus($shippedStatus)
                    ->save();
            }
        }
    }
}
