<?php
/**
 * This file is used to create a settings model in the database
 *
 * @author       Novalnet AG
 * @copyright(C) Novalnet
 * @license      https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
namespace Novalnet\Models;

use Carbon\Carbon;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
use Novalnet\Services\PaymentService;
use Plenty\Plugin\Log\Loggable;

/**
 * Class Settings
 *
 * @property int $id
 * @property int $clientId
 * @property int $pluginSetId
 * @property array $value
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @package Novalnet\Models
 */
class Settings extends Model
{
    use Loggable;

    public $id;
    public $clientId;
    public $pluginSetId;
    public $value = [];
    public $createdAt = '';
    public $updatedAt = '';

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'Novalnet::settings';
    }

    /**
     * Insert the configuration values into settings table
     *
     * @param array $data
     *
     * @return Model
     */
    public function create($data)
    {
        $this->clientId    = $data['clientId'];
        $this->pluginSetId = $data['pluginSetId'];
        $this->createdAt   = (string)Carbon::now();
        $this->value = [
            'novalnet_public_key'           => $data['novalnet_public_key'],
            'novalnet_private_key'          => $data['novalnet_access_key'],
            'novalnet_tariff_id'            => $data['novalnet_tariff_id'],
            'novalnet_client_key'           => $data['novalnet_client_key'],
            'novalnet_order_creation'       => $data['novalnet_order_creation'],
            'novalnet_webhook_testmode'     => $data['novalnet_webhook_testmode'],
            'novalnet_webhook_email_to'     => $data['novalnet_webhook_email_to'],
        ];
        return $this->save();
    }

    /**
     * Update the configuration values into settings table
     *
     * @param array $data
     *
     * @return Model
     */
    public function update($data)
    {
        if(isset($data['novalnet_public_key'])) {
            $this->value['novalnet_public_key'] = $data['novalnet_public_key'];
        }
        if(isset($data['novalnet_private_key'])) {
            $this->value['novalnet_private_key'] = $data['novalnet_private_key'];
        }
        if(isset($data['novalnet_tariff_id'])) {
            $this->value['novalnet_tariff_id']  = $data['novalnet_tariff_id'];
        }
        if(isset($data['novalnet_client_key'])) {
            $this->value['novalnet_client_key'] = $data['novalnet_client_key'];
        }
        if(isset($data['novalnet_order_creation'])) {
            $this->value['novalnet_order_creation'] = $data['novalnet_order_creation'];
        }
        if(isset($data['novalnet_webhook_testmode'])) {
            $this->value['novalnet_webhook_testmode'] = $data['novalnet_webhook_testmode'];
        }
        if(isset($data['novalnet_webhook_email_to'])) {
            $this->value['novalnet_webhook_email_to'] = $data['novalnet_webhook_email_to'];
        }      
        return $this->save();
    }

    /**
     * Save the configuration values into settings table
     *
     * @return Model
     */
    public function save()
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);
        $this->updatedAt = (string)Carbon::now();
        $paymentService = pluginApp(PaymentService::class);
        // Update the Novalnet API version 
        $paymentService->updateApiVersion($this->value);
        // Log the configuration updated time for the reference
        $this->getLogger(__METHOD__)->error('Updated Novalnet settings details ' . $this->updatedAt, $this);
        return $database->save($this);
    }

    /**
     * Delete the configuration values into settings table
     *
     * @return bool
     */
    public function delete()
    {
        /** @var DataBase $database */
        $database = pluginApp(DataBase::class);
        return $database->delete($this);
    }
}
