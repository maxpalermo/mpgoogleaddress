<?php
/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Massimiliano Palermo <info@mpsoft.it>
*  @copyright 2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');

if (Tools::isSubmit('ajax') && tools::isSubmit('action') && tools::isSubmit('token')) {
    $myToken = Tools::encrypt('mpgoogleaddress');
    $ajaxToken = Tools::getValue('token', '');
    $id_order = (int)Tools::getValue('id_order');
    $type = Tools::getValue('type');
    $action = Tools::getValue('action');

    if ($myToken != $ajaxToken) {
        print 'INVALID TOKEN';
        exit();
    }
    if ($action!='showAddress') {
        Tools::dieObject('INVALID ACTION');
    }
    $order = new Order($id_order);
    if ($type == 'delivery') {
        $id_address = $order->id_address_delivery;
    } else {
        $id_address = $order->id_address_invoice;
    }
    showAddress($id_address);
}

function showAddress($id_address)
{
    $address = new Address($id_address);
    $state = new State($address->id_state);

    print Tools::jsonEncode(
        array(
            'result' => true,
            'method' => 'ajaxShowAddress',
            'address' => array(
                'address1' => $address->address1,
                'postcode' => $address->postcode,
                'city' => $address->city,
                'state' => $state->name,
                'state_iso_code' => $state->iso_code,
                'country' => $address->country,
            ),
        )
    );
    exit();
}
