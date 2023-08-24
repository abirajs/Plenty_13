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
	if($settingsService->getPaymentSettingsValue('novalnet_payment_active') == true) {
	$this->getLogger(__METHOD__)->error('yes', 'yes');
        return $twig->render('Novalnet::NovalnetOrderPayment',
                            [
                                'transactionComments' => 'transactioncomments',
                                'cashpaymentToken' => 'cashpayment',
                            ]);
	} else {
	$this->getLogger(__METHOD__)->error('no', 'yes');
	return '';	
	}
    }
}
