<?php
/**
 * This file is used for retrieve the details from the  shop instance
 *
 * @author       Novalnet AG
 * @copyright(C) Novalnet
 * @license      https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
namespace Novalnet\Helper;

use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;

/**
 * Class PaymentHelper
 *
 * @package Novalnet\Helper
 */
class PaymentHelper
{
    /**
     * @var AddressRepositoryContract
     */
    private $addressRepository;
    
    /**
     * Constructor.
     *
     * @param PaymentMethodRepositoryContract $paymentMethodRepository
     * @param AddressRepositoryContract $addressRepository
     * 
     */
    public function __construct(PaymentMethodRepositoryContract $paymentMethodRepository,
								AddressRepositoryContract $addressRepository,
                             
                                )
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->addressRepository                = $addressRepository;
    }

    /**
     * Load the ID of the payment method
     * Return the ID for the payment method found
     *
     * @param string $paymentKey
     *
     * @return string|int
     */
    public function getPaymentMethodByKey()
    {
        $paymentMethods = $this->paymentMethodRepository->allForPlugin('plenty_novalnet');
        if(!is_null($paymentMethods)) {
            foreach($paymentMethods as $paymentMethod) {
                if($paymentMethod->paymentKey == 'NOVALNET') {
                    return [$paymentMethod->id, $paymentMethod->paymentKey, $paymentMethod->name];
                }
            }
        }
        return 'no_paymentmethod_found';
    }
    
    /**
     * Load the ID of the payment method
     * Return the payment key for the payment method found
     *
     * @param int $mop
     *
     * @return string|bool
     */
    public function getPaymentKeyByMop($mop)
    {
        $paymentMethods = $this->paymentMethodRepository->allForPlugin('plenty_novalnet');
        if(!is_null($paymentMethods)) {
            foreach($paymentMethods as $paymentMethod) {
                if($paymentMethod->id == $mop) {
                    return $paymentMethod->paymentKey;
                }
            }
        }
        return false;
    }
    
    /**
     * Get the payment method class
     *
     * @return array
     */
    public static function getPaymentMethods()
    {
        return [
            Novalnet::PAYMENT_KEY   => 	Novalnet::class,
        ];
    }

    /**
     * Get the payment method class
     *
     * @return array
     */
    public function getPaymentMethodsKey()
    {
        return [
             Novalnet::PAYMENT_KEY,
        ];
    }
    
    /**
     * Get billing/shipping address by its id
     *
     * @param int $addressId
     *
     * @return object
     */
    public function getCustomerAddress(int $addressId)
    {
        try {
            /** @var \Plenty\Modules\Authorization\Services\AuthHelper $authHelper */
            $authHelper = pluginApp(AuthHelper::class);
            $addressDetails = $authHelper->processUnguarded(function () use ($addressId) {
                //unguarded
               return $this->addressRepository->findAddressById($addressId);
            });
            return $addressDetails;
        } catch (\Exception $e) {
            $this->getLogger(__METHOD__)->alert('Novalnet::getCustomerAddress', $e);
        }
    }
  
     /**
     * Convert the orderamount to cents
     *
     * @param float $amount
     *
     * @return string
     */
    public function convertAmountToSmallerUnit($amount)
    {
        return sprintf('%0.2f', $amount) * 100;
    }
    
        /**
     * Execute curl process
     *
     * @param string $paymentRequestData
     * @param string $paymentUrl
     * @param string $paymentAccessKey
     *
     * @return array
     */
    public function executeCurl($paymentRequestData, $paymentUrl, $paymentAccessKey)
    {
        // Setting up the important information in the headers
        $headers = [
            'Content-Type:application/json',
            'charset:utf-8',
            'X-NN-Access-Key:'. base64_encode($paymentAccessKey),
        ];
        try {
            $curl = curl_init();
            // Set cURL options
            curl_setopt($curl, CURLOPT_URL, $paymentUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($paymentRequestData));
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            // Execute cURL
            $paymentResponse = curl_exec($curl);
            // Handle cURL error
            if(!empty(curl_errno($curl))) {
               $this->getLogger(__METHOD__)->error('Novalnet::executeCurlError', curl_errno($curl) .' '. curl_error($curl));
            }
            // Close cURL
            curl_close($curl);
            // Decoding the JSON string to array for further processing
            return json_decode($paymentResponse, true);
        } catch (\Exception $e) {
            $this->getLogger(__METHOD__)->error('Novalnet::executeCurlError', $e);
        }
    }
    
}
