<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Integration\Observer;

use Magento\Sales\Model\Order;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use TIG\PostNL;
use TIG\PostNL\Observer\TIGPostNLShipmentSaveAfter\CreatePostNLShipment;
use TIG\PostNL\Test\Integration\TestCase;

class CreatePostNLShipmentTest extends TestCase
{
    protected $instanceClass = CreatePostNLShipment::class;

    /**
     * @magentoDataFixture Magento/Sales/_files/shipment.php
     */
    public function testPostNLShipmentIsCreated()
    {
        $this->markTestSkipped('Should be fixed');

        $shipment = $this->getShipment();
        $this->createOrder($shipment);

        /** @var Observer $observer */
        $observer = $this->getObject(Observer::class);
        $observer->setData('data_object', $shipment);

        $barcodeMock = $this->getFakeMock('TIG\PostNL\Webservices\Endpoints\Barcode');
        $barcodeMock->setMethods(['call']);
        $barcodeMock = $barcodeMock->getMock();

        $callExpects = $barcodeMock->expects($this->once());
        $callExpects->method('call');
        $callExpects->willReturn((Object)['Barcode' => '3STOTA1234567890']);

        /** @var CreatePostNLShipment $instance */
        $instance = $this->getInstance(['barcode' => $barcodeMock]);
        $instance->execute($observer);

        $postnlShipment = $this->getPostNLShipment($shipment);

        $this->assertEquals($shipment->getId(), $postnlShipment->getData('shipment_id'));
    }

    /**
     * @return Order\Shipment
     */
    protected function getShipment()
    {
        /** @var OrderCollection $orderCollection */
        $orderCollection = $this->getObject(OrderCollection::class);
        $orderCollection->addFieldToFilter('customer_email', 'customer@null.com');

        /** @var Order $order */
        $order = $orderCollection->getFirstItem();

        /** @var Order\Shipment $shipment */
        $shipment = $order->getShipmentsCollection()->getFirstItem();

        return $shipment;
    }

    /**
     * @param Order\Shipment $shipment
     */
    private function createOrder(Order\Shipment $shipment)
    {
        $orderId = $shipment->getOrderId();

        /** @var OrderFactory $factory */
        $factory = $this->getObject(\TIG\PostNL\Model\OrderFactory::class);
        $order = $factory->create();
        $order->setData('order_id', $orderId);
        $order->save();
    }

    /**
     * @param $shipment
     *
     * @return \Magento\Framework\DataObject
     */
    protected function getPostNLShipment($shipment)
    {
        /** @var PostNL\Model\ShipmentFactory $shipmentFactory */
        $shipmentFactory = $this->objectManager->create(PostNL\Model\ShipmentFactory::class);

        /** @var PostNL\Model\ResourceModel\Shipment\Collection $collection */
        $collection = $shipmentFactory->create()->getCollection();
        $collection->addFieldToFilter('shipment_id', $shipment->getId());

        $postnlShipment = $collection->getFirstItem();

        return $postnlShipment;
    }
}
