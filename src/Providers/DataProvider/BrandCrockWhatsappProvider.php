<?php
/**
 * This file is used for registering the BrandCrock payment methods
 * and Event procedures
 *
 * @author       BrandCrock GMBH
 * @copyright(C) BrandCrock
 * @license      https://www.brandcrock.de/payment-plugins/kostenlos/lizenz
 */
namespace BrandCrockWhatsapp\Providers\DataProvider;

use BrandCrockWhatsapp\Services\SettingsService;
use BrandCrockWhatsapp\Helper\WhatsappHelper;
use Plenty\Plugin\ServiceProvider;
use Plenty\Plugin\Templates\Twig;
use Plenty\Plugin\Log\Loggable;

class BrandCrockWhatsappProvider extends ServiceProvider
{
    use Loggable;
    /**
     * Register the service provider.
     */
    public function call(Twig $twig):string
    {
    $settingsService        = pluginApp(SettingsService::class);
    $whatsappHelper         = pluginApp(WhatsappHelper::class);

    $enableChat         = $settingsService->getPaymentSettingsValue('bc_whatsapp_enable_chat');
    $chatHeading        = $settingsService->getPaymentSettingsValue('bc_whatsapp_chat_heading');
    $chatDescription    = $settingsService->getPaymentSettingsValue('bc_whatsapp_chat_description');
    $accountName        = $settingsService->getPaymentSettingsValue('bc_whatsapp_account_name');
    $accountRole        = $settingsService->getPaymentSettingsValue('bc_whatsapp_account_role');
    $mobileNumber       = $settingsService->getPaymentSettingsValue('bc_whatsapp_mobile_number');
    $profileLogo        = $settingsService->getPaymentSettingsValue('bc_whatsapp_profile_logo');
    $openNewTab         = $settingsService->getPaymentSettingsValue('bc_whatsapp_open_new_tab');
    $desktopURL         = $settingsService->getPaymentSettingsValue('bc_whatsapp_url_desktop');
    $mobileURL      = $settingsService->getPaymentSettingsValue('bc_whatsapp_url_mobile');
    $mobileTheme        = $settingsService->getPaymentSettingsValue('bc_whatsapp_mobile_theme');
    $mobileShape        = $settingsService->getPaymentSettingsValue('bc_whatsapp_mobile_shape');
    $desktopTheme       = $settingsService->getPaymentSettingsValue('bc_whatsapp_desktop_theme');
    $desktopShape       = $settingsService->getPaymentSettingsValue('bc_whatsapp_desktop_shape');
    $isMobile       = $whatsappHelper->isMobile();

    if($enableChat == 'true') {
        return $twig->render('BrandCrockWhatsapp::BrandCrockWhatsappDataProvider',
                    [
                        'accountName'       =>  $accountName,
                        'accountRole'       =>  $accountRole,
                        'chatHeading'       =>  $chatHeading,
                        'chatDescription'   =>  $chatDescription,
                        'mobileNumber'      =>  $mobileNumber,
                        'profileLogo'       =>  $profileLogo,
                        'openNewTab'        =>  $openNewTab,
                        'desktopURL'        =>  $desktopURL,
                        'mobileURL'         =>  $mobileURL,
                        'mobileTheme'       =>  $mobileTheme,
                        'mobileShape'       =>  $mobileShape,
                        'desktopTheme'      =>  $desktopTheme,
                        'desktopShape'      =>  $desktopShape,
                        'isMobile'          =>  $isMobile,
                    ]);
    } else {
        return '';
    }
    }
}
