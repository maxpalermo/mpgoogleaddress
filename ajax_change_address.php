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
    $id_address = (int)Tools::getValue('id_address');
    $type = Tools::getValue('type');
    $action = Tools::getValue('action');

    if ($myToken != $ajaxToken) {
        print 'INVALID TOKEN';
        exit();
    }
    if ($action!='changeAddress') {
        Tools::dieObject('INVALID ACTION');
    }
    changeAddress($id_order, $id_address, $type);
}

function changeAddress($id_order, $id_address, $type)
{
    $order = new Order($id_order);
    if ($type=="delivery") {
        $order->id_address_delivery = $id_address;
    } else {
        $order->id_address_invoice = $id_address;
    }
    $result = $order->save();
    print Tools::jsonEncode(
        array(
            'result' => $result,
            'method' => 'ajaxDisplayChangeAddress'
        )
    );
    exit();
}
