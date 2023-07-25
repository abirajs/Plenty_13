<?php
/**
 * This file is act as helper for the Novalnet payment plugin
 *
 * @author       Novalnet AG
 * @copyright(C) Novalnet
 * @license      https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
namespace Novalnet\Services;

use Novalnet\Helper\PaymentHelper;
use Novalnet\Services\SettingsService;
use Plenty\Modules\Basket\Models\Basket;
use Novalnet\Constants\NovalnetConstants;
use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;

/**
 * Class PaymentService
 *
 * @package Novalnet\Services
 */
class PaymentService
{
	/**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;
	 /**
     * @var AddressRepositoryContract
     */
    private $addressRepository;
    /**
     * Constructor.
     *
     * @param PaymentHelper $paymentHelper
     * @param AddressRepositoryContract $addressRepository
     * 
     */
    public function __construct( PaymentHelper $paymentHelper, 
					SettingsService $settingsService,
					AddressRepositoryContract $addressRepository,                         
                               )
    {  
	   $this->settingsService      = $settingsService;      
       $this->paymentHelper  = 	$paymentHelper;
       $this->addressRepository    = $addressRepository;
    }

    /**
     * Check if the merchant details configured
     *
     * @return bool
     */
    public function isMerchantConfigurationValid()
    {
        return (bool) ($this->settingsService->getPaymentSettingsValue('novalnet_public_key') != '' && $this->settingsService->getPaymentSettingsValue('novalnet_private_key') != ''
        && $this->settingsService->getPaymentSettingsValue('novalnet_tariff_id') != '');
    }

    /**
     * Show payment for allowed countries
     *
     * @param  object $basket
     * @param string $allowedCountry
     *
     * @return bool
     */
    public function allowedCountries(Basket $basket, $allowedCountry)
    {
        $allowedCountry = str_replace(' ', '', strtoupper($allowedCountry));
        $allowedCountryArray = explode(',', $allowedCountry);
        try {
            if(!is_null($basket) && $basket instanceof Basket && !empty($basket->customerInvoiceAddressId)) {
                $billingAddressId = $basket->customerInvoiceAddressId;
                $billingAddress = $this->paymentHelper->getCustomerAddress((int) $billingAddressId);
                $country = $this->countryRepository->findIsoCode($billingAddress->countryId, 'iso_code_2');
                if(!empty($billingAddress) && !empty($country) && in_array($country, $allowedCountryArray)) {
                        return true;
                }
            }
        } catch(\Exception $e) {
            return false;
        }
        return false;
    }

    /**
     * Show payment for Minimum Order Amount
     *
     * @param object $basket
     * @param int $minimumAmount
     *
     * @return bool
     */
    public function getMinBasketAmount(Basket $basket, $minimumAmount)
    {
        if(!is_null($basket) && $basket instanceof Basket) {
            $amount = $this->paymentHelper->convertAmountToSmallerUnit($basket->basketAmount);
            if(!empty($minimumAmount) && $minimumAmount <= $amount) {
                return true;
            }
        }
        return false;
    }

    /**
     * Show payment for Maximum Order Amount
     *
     * @param object $basket
     * @param int $maximumAmount
     *
     * @return bool
     */
    public function getMaxBasketAmount(Basket $basket, $maximumAmount)
    {
        if(!is_null($basket) && $basket instanceof Basket) {
            $amount = $this->paymentHelper->convertAmountToSmallerUnit($basket->basketAmount);
            if(!empty($maximumAmount) && $maximumAmount >= $amount) {
                return true;
            }
        }
        return false;
    }
    
        /**
     * Update the payment processing API version
     *
     * @param array $merchantRequestData
     *
     * @return none
     */
    public function updateApiVersion($merchantRequestData)
    {
        $paymentRequestData = [];
        // Build the merchant Data
        $paymentRequestData['merchant'] = ['signature' => $merchantRequestData['novalnet_public_key']];
        // Build the Custom Data
        $paymentRequestData['custom'] = ['lang' => 'DE'];
        $paymentResponseData = $this->paymentHelper->executeCurl($paymentRequestData, NovalnetConstants::MERCHANT_DETAILS, $merchantRequestData['novalnet_private_key']);
        if($paymentResponseData['result']['status'] == 'SUCCESS') {
            $this->getLogger(__METHOD__)->error('Novalnet::updateApiVersion', 'Novalnet API Version updated successfully');
        } else {
            $this->getLogger(__METHOD__)->error('Novalnet::updateApiVersion failed', $paymentResponseData);
        }
   }
}
