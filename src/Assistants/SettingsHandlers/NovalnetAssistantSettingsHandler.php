<?php
/**
 * This file is used to save all data created during 
 * the assistant process
 *
 * @author       Novalnet AG
 * @copyright(C) Novalnet
 * @license      https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
namespace Novalnet\Assistants\SettingsHandlers;

use Novalnet\Helper\PaymentHelper;
use Novalnet\Services\SettingsService;
use Plenty\Modules\Plugin\PluginSet\Contracts\PluginSetRepositoryContract;
use Plenty\Modules\Wizard\Contracts\WizardSettingsHandler;

/**
 * Class NovalnetAssistantSettingsHandler
 *
 * @package Novalnet\Assistants\SettingsHandlers
 */
class NovalnetAssistantSettingsHandler implements WizardSettingsHandler
{
    public function handle(array $postData)
    {
        /** @var PluginSetRepositoryContract $pluginSetRepo */
        $pluginSetRepo = pluginApp(PluginSetRepositoryContract::class);
        /** @var PaymentHelper $paymentHelper */
        $paymentHelper = pluginApp(PaymentHelper::class);
        $clientId = $postData['data']['clientId'];
        $pluginSetId = $pluginSetRepo->getCurrentPluginSetId();
        $data = $postData['data'];
        // Novalnet global and webhook configuration values
        $novalnetSettings=[
            'novalnet_public_key'       =>  $data['novalnetPublicKey'] ?? '',
            'novalnet_private_key'      =>  $data['novalnetAccessKey'] ?? '',
            'novalnet_tariff_id'        =>  $data['novalnetTariffId'] ?? '',
            'novalnet_client_key'       =>  $data['novalnetClientKey'] ?? '',
            'novalnet_order_creation'   =>  $data['novalnetOrderCreation'] ?? '',
            'novalnet_webhook_testmode' =>  $data['novalnetWebhookTestMode'] ?? '',
            'novalnet_webhook_email_to' =>  $data['novalnetWebhookEmailTo'] ?? '',
        ];
        
        // Payment method common configuration values
        foreach($paymentHelper->getPaymentMethodsKey() as $paymentMethodKey) {
            $paymentKey=str_replace('_','',ucwords(strtolower($paymentMethodKey),'_'));
            $paymentKey[0] = strtolower($paymentKey[0]);
            $paymentMethodKey = strtolower($paymentMethodKey);
            $novalnetSettings[$paymentMethodKey]['payment_active']               = $data[$paymentKey . 'PaymentActive'] ?? '';
            $novalnetSettings[$paymentMethodKey]['test_mode']                    = $data[$paymentKey . 'TestMode'] ?? '';
            $novalnetSettings[$paymentMethodKey]['payment_logo']                 = $data[$paymentKey . 'PaymentLogo'] ?? '';
		}
        /** @var SettingsService $settingsService */
        $settingsService=pluginApp(SettingsService::class);
        $settingsService->updateSettings($novalnetSettings, $clientId, $pluginSetId);
        return true;
    }
}
