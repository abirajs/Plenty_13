<?php
/**
 * This file is used for displaying transaction comments in the
 * order confirmation page
 *
 * @author       Novalnet AG
 * @copyright(C) Novalnet
 * @license      https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
namespace Novalnet\Providers;

use Plenty\Plugin\Templates\Twig;
use Novalnet\Helper\PaymentHelper;
use Novalnet\Services\PaymentService;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Novalnet\Services\SettingsService;
use Plenty\Plugin\Log\Loggable;

/**
 * Class NovalnetOrderConfirmationDataProvider
 *
 * @package Novalnet\Providers\DataProvider
 */
class NovalnetOrderConfirmationDataProvider
{
	use Loggable;
    /**
     * Displaying transaction comments in the order confirmation page
     *
     * @param Twig $twig
     * @param PaymentRepositoryContract $paymentRepositoryContract
     * @param Arguments $arg
     *
     * @return string
     */
    public function call(Twig $twig,
                         PaymentRepositoryContract $paymentRepositoryContract,
                         $arg
                        )
    {
	$settingsService    = pluginApp(SettingsService::class);
	    $paymentHelper      = pluginApp(PaymentHelper::class);
	if($settingsService->getPaymentSettingsValue('novalnet_payment_active') == true) {
	$this->getLogger(__METHOD__)->error('yes', 'yes');
		 $paymentRequestData['transaction'] = [
							'amount' => 4955,
							'currency' => 'EUR',
							'test_mode' => 1,
						];
						$paymentRequestData['transaction']['hosted_page'] = [
							'type' => 'PAYMENTFORM',
						];
						$paymentRequestData['merchant'] = [
							'signature' => '7ibc7ob5|tuJEH3gNbeWJfIHah||nbobljbnmdli0poys|doU3HJVoym7MQ44qf7cpn7pc',
							'tariff' => '10004',
						];
						$paymentRequestData['customer'] = [
							'first_name' => 'PAYMENTFORM',
							'last_name' => 'PAYMENTFORM',
							'email' => 'test@gmail.com',
							'customer_ip' => '125.21.64.250',
							
						];
						$paymentRequestData['billing'] = [
							'street' => 'test',
							'city' => 'test',
							'country_code' => 'DE',
							'zip' => '54570',
							
						];
						$paymentRequestData['shipping'] = [
							'street' => 'test',
							'city' => 'test',
							'country_code' => 'DE',
							'zip' => '54570',
						];
						$paymentRequestData['custom'] = [
							'lang' => 'EN',

						];
						
						$paymentResponseData = $paymentHelper->executeCurl($paymentRequestData, 'https://payport.novalnet.de/v2/seamless/payment', 'a87ff679a2f3e71d9181a67b7542122c');
						$this->getLogger(__METHOD__)->error('Adding PDF comment failed for order ' , $paymentResponseData);
						$paymentFormUrl = $paymentResponseData['result']['redirect_url'];
        return $twig->render('Novalnet::NovalnetOrderPayment',
                            [
                                'transactionComments' => 'transactioncomments',
                                'cashpaymentToken' => 'cashpayment',
			     'url' => $paymentFormUrl
                            ]);
	} else {
	$this->getLogger(__METHOD__)->error('no', 'yes');
	return '';	
	}
    }
}
