<?php

/**
 * PAYONE Magento 2 Connector is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PAYONE Magento 2 Connector is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with PAYONE Magento 2 Connector. If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category  Payone
 * @package   Payone_Magento2_Plugin
 * @author    FATCHIP GmbH <support@fatchip.de>
 * @copyright 2003 - 2017 Payone GmbH
 * @license   <http://www.gnu.org/licenses/> GNU Lesser General Public License
 * @link      http://www.payone.de
 */

namespace Payone\Core\Test\Unit\Observer\Transactionstatus;

use Payone\Core\Observer\Transactionstatus\Paid as ClassToTest;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\Invoice;
use Payone\Core\Helper\Base;

class PaidTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassToTest
     */
    private $classToTest;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $invoice = $this->getMockBuilder(Invoice::class)->disableOriginalConstructor()->getMock();

        $invoiceService = $this->getMockBuilder(InvoiceService::class)->disableOriginalConstructor()->getMock();
        $invoiceService->method('prepareInvoice')->willReturn($invoice);

        $baseHelper = $this->getMockBuilder(Base::class)->disableOriginalConstructor()->getMock();
        $baseHelper->method('getConfigParam')->willReturn('1');

        $this->classToTest = $this->objectManager->getObject(ClassToTest::class, [
            'invoiceService' => $invoiceService,
            'baseHelper' => $baseHelper
        ]);
    }

    public function testExecute()
    {
        $payment = $this->getMockBuilder(OrderPaymentInterface::class)->disableOriginalConstructor()->getMock();
        $payment->method('getLastTransId')->willReturn('123');

        $order = $this->getMockBuilder(Order::class)->disableOriginalConstructor()->getMock();
        $order->method('getPayment')->willReturn($payment);

        $observer = $this->getMockBuilder(Observer::class)->disableOriginalConstructor()->setMethods(['getOrder'])->getMock();
        $observer->method('getOrder')->willReturn($order);

        $result = $this->classToTest->execute($observer);
        $this->assertNull($result);
    }

    public function testExecuteNoOrder()
    {
        $observer = $this->getMockBuilder(Observer::class)->disableOriginalConstructor()->setMethods(['getOrder'])->getMock();
        $observer->method('getOrder')->willReturn(null);

        $result = $this->classToTest->execute($observer);
        $this->assertNull($result);
    }
}
