<?php
/**
 * @category   Sales
 * @package    Quafzi_AutoShippingStatus
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Thomas Birke <tbirke@netextreme.de>
 */
class Quafzi_AutoShippingStatus_Test_Model_ObserverTest
    extends EcomDev_PHPUnit_Test_Case
{
    protected $sut;

    protected $changeableStatuses = [
        'not touched',
        'unchanged'
    ];

    protected $targetStatusComplete = 'shipped';

    protected $targetStatusPartial  = 'partial';

    public function setUp()
    {
        $this->sut = Mage::getModel('autoshippingstatus/observer');
        return parent::setUp();
    }

    public function testClassExists()
    {
        $this->assertInstanceOf(
            'Quafzi_AutoShippingStatus_Model_Observer',
            $this->sut
        );
    }

    public function testStillIncompleteShipment()
    {
        $this->setSwitchToPartial(false);
        $shipment = $this->getShipment($canShip = true, $expectChange = false);
        $this->assertEquals('not touched', $shipment->getOrder()->getStatus());

        $this->setSwitchToPartial(true);
        $shipment = $this->getShipment($canShip = true, $expectChange = true);
        $this->assertEquals('partial', $shipment->getOrder()->getStatus());
    }

    public function testCompleteShipment()
    {
        $this->setSwitchToShipped(false);
        $shipment = $this->getShipment($canShip = false, $expectChange = false);
        $this->assertEquals('not touched', $shipment->getOrder()->getStatus());

        $this->setSwitchToShipped(true);
        $shipment = $this->getShipment($canShip = false, $expectChange = true);
        $this->assertEquals('shipped', $shipment->getOrder()->getStatus());
    }

    protected function getShipment($canShip, $expectChange)
    {
        $this->setCompleteSourceStatuses($this->changeableStatuses);
        $this->setCompleteTargetStatus($this->targetStatusComplete);
        $this->setPartialSourceStatuses($this->changeableStatuses);
        $this->setPartialTargetStatus($this->targetStatusPartial);

        $order = $this->getModelMock(
            'sales/order',
            array('canShip', 'save')
        );
        $order->expects($this->any())
            ->method('canShip')
            ->will($this->returnValue($canShip));
        $order->expects($expectChange ? $this->once() : $this->never())
            ->method('save');
        $order->setStatus(current($this->changeableStatuses));
        $shipment = Mage::getModel('sales/order_shipment')
            ->setOrder($order);
        Mage::dispatchEvent(
            'sales_order_shipment_save_after',
            array('shipment' => $shipment)
        );
        return $shipment;
    }

    protected function setSwitchToPartial($enable) {
        Mage::app()->getStore()->setConfig(
            'shipping/option/switchToPartial',
            $enable
        );
    }

    protected function setPartialSourceStatuses($statuses) {
        Mage::app()->getStore()->setConfig(
            'shipping/option/statusBeforeSwitchToPartial',
            implode(',', $statuses)
        );
    }

    protected function setPartialTargetStatus($status) {
        Mage::app()->getStore()->setConfig(
            'shipping/option/statusAfterSwitchToPartial',
            $status
        );
    }

    protected function setSwitchToShipped($enable) {
        Mage::app()->getStore()->setConfig(
            'shipping/option/switchToShipped',
            $enable
        );
    }

    protected function setCompleteSourceStatuses($statuses) {
        Mage::app()->getStore()->setConfig(
            'shipping/option/statusBeforeSwitchToShipped',
            implode(',', $statuses)
        );
    }

    protected function setCompleteTargetStatus($status) {
        Mage::app()->getStore()->setConfig(
            'shipping/option/statusAfterSwitchToShipped',
            $status
        );
    }
}
