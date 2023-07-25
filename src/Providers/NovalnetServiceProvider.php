<?php
/**
 * This file is used for registering the Novalnet payment methods
 * and Event procedures
 *
 * @author       Novalnet GMBH
 * @copyright(C) Novalnet
 * @license      https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
namespace Novalnet\Providers;

use Novalnet\Helper\PaymentHelper;
use Novalnet\Services\PaymentService;
use Novalnet\Assistants\NovalnetAssistant;
use Plenty\Plugin\ServiceProvider;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\Templates\Twig;
use Plenty\Modules\Wizard\Contracts\WizardContainerContract;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodContainer;
use Plenty\Modules\Basket\Events\Basket\AfterBasketCreate;
use Plenty\Modules\Basket\Events\Basket\AfterBasketChanged;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemAdd;

class NovalnetServiceProvider extends ServiceProvider
{
   /**
    * Boot additional services for the payment method
    *
    * @param Dispatcher $eventDispatcher
    * @param Twig $twig
    * @param PaymentMethodContainer $payContainer
    * @param PaymentHelper $paymentHelper
    * @param PaymentService $paymentService
    * 
    */
    public function boot( PaymentMethodContainer $payContainer,
						  PaymentHelper $paymentHelper,
						  PaymentService $paymentService,
						  Dispatcher $eventDispatcher,
						  Twig $twig,
						)
    {
		// Set the Novalnet Assistant
		pluginApp(WizardContainerContract::class)->register('payment-novalnet-assistant', NovalnetAssistant::class);
    }
    

}
