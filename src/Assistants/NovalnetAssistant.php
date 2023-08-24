<?php
/**
 * This file is used for creating the configuration for the plugin
 *
 * @author       Novalnet AG
 * @copyright(C) Novalnet
 * @license      https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */

namespace Novalnet\Assistants;

use Novalnet\Assistants\SettingsHandlers\NovalnetAssistantSettingsHandler;
use Novalnet\Helper\PaymentHelper;
use Plenty\Modules\Wizard\Services\WizardProvider;
use Plenty\Modules\System\Contracts\WebstoreRepositoryContract;
use Plenty\Plugin\Application;
use Plenty\Plugin\Log\Loggable;

/**
 * Class NovalnetAssistant
 *
 * @package Novalnet\Assistants
 */
class NovalnetAssistant extends WizardProvider
{
    use Loggable;

    /**
     * @var WebstoreRepositoryContract
     */
    private $webstoreRepository;

    /**
     * @var $mainWebstore
     */
    private $mainWebstore;

    /**
     * @var $webstoreValues
     */
    private $webstoreValues;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
    * Constructor.
    *
    * @param WebstoreRepositoryContract $webstoreRepository
    * @param PaymentHelper $paymentHelper
    */
    public function __construct(WebstoreRepositoryContract $webstoreRepository,
                                PaymentHelper $paymentHelper
                               )
    {
        $this->webstoreRepository   = $webstoreRepository;
        $this->paymentHelper        = $paymentHelper;
     }

    protected function structure()
    {
        $config =
        [
            "title" => 'NovalnetAssistant.novalnetAssistantTitle',
            "shortDescription" => 'NovalnetAssistant.novalnetAssistantShortDescription',
            "iconPath" => $this->getIcon(),
            "settingsHandlerClass" => NovalnetAssistantSettingsHandler::class,
            "translationNamespace" => 'Novalnet',
            "key" => 'payment-novalnet-assistant',
            "topics" => ['payment'],
            "priority" => 990,
            "options" =>
            [
                'clientId' =>
                [
                    'type'          => 'select',
                    'defaultValue'  => $this->getMainWebstore(),
                    'options'       => [
                                        'name'          => 'NovalnetAssistant.clientId',
                                        'required'      => true,
                                        'listBoxValues' => $this->getWebstoreListForm(),
                                       ],
                ],
            ],
            "steps" => []
        ];

        $config = $this->createGlobalConfiguration($config);
        $config = $this->createWebhookConfiguration($config);
        $config = $this->createPaymentMethodConfiguration($config);
        return $config;
    }

   /**
     * Load Novalnet Icon
     *
     * @return string
     */
    protected function getIcon()
    {
        $app = pluginApp(Application::class);
        $icon = $app->getUrlPath('Novalnet').'/images/novalnet_icon.png';
        return $icon;
    }

    /**
     * Load main web store configuration
     *
     * @return string
     */
    private function getMainWebstore()
    {
        if($this->mainWebstore === null) {
            $this->mainWebstore = $this->webstoreRepository->findById(0)->storeIdentifier;
        }
        return $this->mainWebstore;
    }

    /**
     * Get the shop list
     *
     * @return array
     */
    private function getWebstoreListForm()
    {
        if($this->webstoreValues === null) {
            $webstores = $this->webstoreRepository->loadAll();
            $this->webstoreValues = [];
            /** @var Webstore $webstore */
            foreach($webstores as $webstore) {
                $this->webstoreValues[] = [
                    "caption" => $webstore->name,
                    "value" => $webstore->storeIdentifier,
                ];
            }
        }
        return $this->webstoreValues;
    }

    /**
    * Create the global configuration
    *
    * @param array $config
    *
    * @return array
    */
    public function createGlobalConfiguration($config)
    {
        $config['steps']['novalnetGlobalConf'] =
        [
            "title" => 'NovalnetAssistant.novalnetGlobalConf',
            "sections" => [
                [
                    "title"         => 'NovalnetAssistant.novalnetGlobalConf',
                    "description"   => 'NovalnetAssistant.novalnetGlobalConfDesc',
                    "form"          =>
                    [
                        'novalnetPublicKey' =>
                        [
                            'type'      => 'text',
                            'options'   => [
                                            'name'      => 'NovalnetAssistant.novalnetPublicKeyLabel',
                                            'tooltip'   => 'NovalnetAssistant.novalnetPublicKeyTooltip',
                                            'required'  => true
                                           ]
                        ],
                        'novalnetAccessKey' =>
                        [
                            'type'      => 'text',
                            'options'   => [
                                            'name'      => 'NovalnetAssistant.novalnetAccessKeyLabel',
                                            'tooltip'   => 'NovalnetAssistant.novalnetAccessKeyTooltip',
                                            'required'  => true
                                           ]
                        ],
                        'novalnetTariffId' =>
                        [
                            'type'      => 'text',
                            'options'   => [
                                            'name'      => 'NovalnetAssistant.novalnetTariffIdLabel',
                                            'tooltip'   => 'NovalnetAssistant.novalnetTariffIdTooltip',
                                            'required'  => true,
                                            'pattern'   => '^[1-9]\d*$'
                                           ]
                        ],
                        'novalnetClientKey' =>
                        [
                            'type'      => 'text',
                            'options'   => [
                                            'name'      => 'NovalnetAssistant.novalnetClientKeyLabel',
                                            'tooltip'   => 'NovalnetAssistant.novalnetClientKeyTooltip',
                                            'required'  => true
                                           ]
                        ],
                        'novalnetOrderCreation' =>
                        [
                            'type'         => 'checkbox',
                            'defaultValue' => true,
                            'options'   => [
                                            'name'  => 'NovalnetAssistant.novalnetOrderCreationLabel'
                                           ]
                        ]
                    ]
                ]
            ]
        ];
        return $config;
    }

    /**
    * Create the webhook configuration
    *
    * @param array $config
    *
    * @return array
    */
    public function createWebhookConfiguration($config)
    {
        $config['steps']['novalnetWebhookConf'] =
        [
                "title"     => 'NovalnetAssistant.novalnetWebhookConf',
                "sections"  =>
                [
                    [
                        "title"         => 'NovalnetAssistant.novalnetWebhookConf',
                        "description"   => 'NovalnetAssistant.novalnetWebhookConfDesc',
                        "form" =>
                        [
                            'novalnetWebhookTestMode' =>
                            [
                                'type'      => 'checkbox',
                                'options'   => [
                                                'name'      => 'NovalnetAssistant.novalnetWebhookTestModeLabel'
                                               ]
                            ],
                            'novalnetWebhookEmailTo' =>
                            [
                                'type'      => 'text',
                                'options'   => [
                                                'name'      => 'NovalnetAssistant.novalnetWebhookEmailToLabel',
                                                'tooltip'   => 'NovalnetAssistant.novalnetWebhookEmailToTooltip'
                                               ]
                            ]
                        ]
                    ]
                ]
        ];
        return $config;
    }
    
        /**
    * Create the payment methods configurations
    *
    * @param array $config
    *
    * @return array
    */
    public function createPaymentMethodConfiguration($config)
    {
       foreach($this->paymentHelper->getPaymentMethodsKey() as $paymentMethodKey) {
          $paymentMethodKey = str_replace('_','',ucwords(strtolower($paymentMethodKey),'_'));
          $paymentMethodKey[0] = strtolower($paymentMethodKey[0]);

          $config['steps'][$paymentMethodKey] =
          [
                "title"     => 'Customize.' . $paymentMethodKey,
                "sections"  =>
                [
                    [
                        "title"         => 'Novalnet',
                        "description"   => 'Novalnet Payment Method',
                        "form"          =>
                        [
                            $paymentMethodKey .'PaymentActive' =>
                            [
                                'type'      => 'checkbox',
                                'options'   => [
                                                'name' => 'NovalnetAssistant.novalnetPaymentActiveLabel'
                                               ]
                            ],                   
                           $paymentMethodKey . 'PaymentLogo' =>
                           [
                                'type'      => 'file',
                                'options'   => [
                                                'name'              => 'NovalnetAssistant.novalnetPaymentLogoLabel',
                                                'showPreview'       => true,
                                                'allowedExtensions' => ['svg', 'png', 'jpg', 'jpeg'],
                                                'allowFolders'      => false
                                               ]
                            ]
                        ]
                    ]
                 ]
          ];

        }
        return $config;
    }

}
