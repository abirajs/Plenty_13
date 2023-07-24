<?php
/**
 * This file is used for creating payment methods
 *
 * @author       Novalnet AG
 * @copyright(C) Novalnet
 * @license      https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
 namespace Novalnet\Migrations;

use Novalnet\Helper\PaymentHelper;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;

/**
 * Class CreateNovalnetPaymentMethods1200
 *
 * @package Novalnet\Migrations
 */
class CreateNovalnetPaymentMethods1200
{
    /**
     * @var PaymentMethodRepositoryContract
     */
    private $paymentMethodRepository;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * Constructor.
     *
     * @param PaymentMethodRepositoryContract $paymentMethodRepository
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(PaymentMethodRepositoryContract $paymentMethodRepository,
                                PaymentHelper $paymentHelper)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * Run on plugin build
     *
     * Create Method of Payment ID for Novalnet payment if they don't exist
     */
    public function run()
    {
            $this->createPaymentMethodByPaymentKey($paymentMethodKey, $paymentMethodName);
    }

    /**
     * Create payment method with given parameters if it doesn't exist
     *
     * @param string $paymentKey
     * @param string $name
     */
    private function createPaymentMethodByPaymentKey($paymentKey, $name)
    {
        $payment_data = $this->paymentHelper->getPaymentMethodByKey($paymentKey);
        if($payment_data == 'no_paymentmethod_found') {
            $paymentMethodData =  ['pluginKey'  => 'plenty_novalnet',
                                  'paymentKey' => 'NOVALNET',
                                  'name'       => 'Novalnet'];
            $this->paymentMethodRepository->createPaymentMethod($paymentMethodData);
        } 
    }
}
