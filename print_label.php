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
require_once(dirname(__FILE__)).'/conf.php';
require_once(dirname(__FILE__).'../../../tools/tcpdf/tcpdf.php');

if (Tools::isSubmit('ajax') && tools::isSubmit('action') && tools::isSubmit('token')) {
    $myToken = Tools::encrypt('mpgoogleaddress');
    $ajaxToken = Tools::getValue('token', '');
    $id_order = (int)Tools::getValue('id_order');
    $id_address = (int)Tools::getValue('id_address');
    $action = Tools::getValue('action');

    if ($myToken != $ajaxToken) {
        print 'INVALID TOKEN';
        exit();
    }
    if ($action!='printLabel') {
        Tools::dieObject('INVALID ACTION');
    }
    
    $cookie = new Cookie('psAdmin');
    $cookie->__set('print_label_id_order', $id_order);
    $cookie->__set('print_label_id_address', $id_address);

} else {
    $cookie = new Cookie('psAdmin');
    $id_order = $cookie->__get('print_label_id_order');
    $id_address = $cookie->__get('print_label_id_address');
    if ($id_order && $id_address) {
        printLabel($id_order, $id_address);
    }
}

function printLabel($id_order, $id_address)
{
    $order = new Order($id_order);
    $address = new Address($id_address);
    $state = new State($address->id_state);
    $showLogo   = Configuration::get('MP_PRINTLABELS_LOGO');
    $logo       = Configuration::get('MP_PRINTLABELS_FILE');

    $values = array(
        'logo' => $showLogo?$logo:null,
        'name' => $address->company?$address->company:$address->firstname.' '.$address->lastname,
        'address1' => $address->address1,
        'address2' => $address->address2,
        'postcode' => $address->postcode,
        'city' => $address->city,
        'state' => $state->name,
        'iso_code' => $state->iso_code,
        'phone' => $address->phone,
        'mobile' => $address->phone_mobile,
        'other' => $address->other,
        'reference' => $order->reference,
    );

    $smarty = Context::getContext()->smarty;
    $smarty->assign($values);

    $label = $smarty->fetch(dirname(__FILE__).'/views/templates/admin/label.tpl');

    // create new PDF document
    $pageLayout = array(100, 100); //  or array($height, $width) 
    $pdf = new TCPDF('p', 'mm', $pageLayout, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Massimiliano Palermo');
    $pdf->SetTitle('TCPDF Address Label');
    $pdf->SetSubject('Label 10x10');
    $pdf->SetKeywords('TCPDF, PDF, digital solutions, prestashop, massimiliano palermo');
    // set auto page breaks
    $pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    // set font
    $pdf->SetFont('dejavusans', '', 10);
    // add a page
    $pdf->SetMargins(10, 10, 10, true);
    //
    $pdf->AddPage();
    // output the HTML content
    $pdf->writeHTML($label, true, false, true, false, '');
    // reset pointer to the last page
    $pdf->lastPage();
    //Close and output PDF document
    header("Content-type:application/pdf");
    $pdf->Output('label.pdf', 'D');
    exit();
}