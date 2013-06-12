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
        $sourcePath = 'shipping/option/partialShipmentStatuses';
        $partialStatuses = explode(',', Mage::getStoreConfig($sourcePath));
        $targetPath = 'shipping/option/shippedStatus';
        $shippedStatus = Mage::getStoreConfig($targetPath);
        if (false == $order->canShip()
            && in_array($order->getStatus(), $partialStatuses)
        ) {
            $order->setStatus($shippedStatus)
                ->save();
        }
    }
}
