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
        $shipment = $this->getShipment($canShip = true);
        $this->assertEquals(
            'not touched',
            $shipment->getOrder()->getStatus()
        );
    }

    public function testCompleteShipment()
    {
        $shipment = $this->getShipment($canShip = false);
        $this->assertEquals('updated', $shipment->getOrder()->getStatus());
    }

    protected function getShipment($canShip)
    {
        $this->setSourceStatuses(array('unchanged', 'not touched'));
        $this->setTargetStatus('updated');
        $order = $this->getModelMock(
            'sales/order',
            array('canShip', 'save')
        );
        $order->expects($this->any())
            ->method('canShip')
            ->will($this->returnValue($canShip));
        $order->expects($canShip ? $this->never() : $this->once())
            ->method('save');
        $order->setStatus('not touched');
        $shipment = Mage::getModel('sales/order_shipment')
            ->setOrder($order);
        Mage::dispatchEvent(
            'sales_order_shipment_save_after',
            array('shipment' => $shipment)
        );
        return $shipment;
    }

    protected function setSourceStatuses($statuses) {
        Mage::app()->getStore()->setConfig(
            'shipping/option/partialShipmentStatuses',
            implode(',', $statuses)
        );
    }

    protected function setTargetStatus($status) {
        Mage::app()->getStore()->setConfig(
            'shipping/option/shippedStatus',
            $status
        );
    }
}
