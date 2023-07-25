<?php
/**
 * This file is used for creating custom Novanet Settings table
 *
 * @author       Novalnet AG
 * @copyright(C) Novalnet
 * @license      https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 */
namespace Novalnet\Migrations;

use Novalnet\Models\Settings;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;

/**
 * Class CreateNovalnetSettingsTable
 *
 * @package Novalnet\Migrations
 */
class CreateNovalnetSettingsTable
{
    /**
     * Create Novalnet Settings table
     *
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(NovalnetSettings::class);
    }
}
