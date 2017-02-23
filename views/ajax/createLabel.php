<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
header('Access-Control-Allow-Origin: *');
header("Content-type: application/json");

$config_path = dirname(__FILE__).'/../../../../config/config.inc.php';
$init_path   = dirname(__FILE__).'/../../../../init.php';

require_once($config_path);
require_once($init_path);

require_once(_PS_TOOL_DIR_.'tcpdf/config/lang/eng.php');
require_once(_PS_TOOL_DIR_.'tcpdf/tcpdf.php');

$_PS_BASE_URL_ = Tools::getCurrentUrlProtocolPrefix().Tools::getShopDomain().__PS_BASE_URI__;
$PageWidth  = Configuration::get('MP_PRINTLABELS_WIDTH'); //millimeters
$PageHeight = Configuration::get('MP_PRINTLABELS_HEIGHT'); //millimeters
$ShowLogo   = Configuration::get('MP_PRINTLABELS_LOGO');
$LogoExt    = Configuration::get('MP_PRINTLABELS_EXT');
$Logo       = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "img" . DIRECTORY_SEPARATOR . "image_logo.dat";
$imageSize  = getimagesize($Logo);
$ShowPhone  = Configuration::get('MP_PRINTLABELS_PHONE');
$ShowMobile = Configuration::get('MP_PRINTLABELS_MOBILE');
$ShowOrder  = Configuration::get('MP_PRINTLABELS_ORDER');

$id_order   = Tools::getValue("id_order");
$addr_type  = Tools::getValue("address_type");
$orderObj   = new Order($id_order);
if ($addr_type=='invoice') {
    $addressObj = new Address($orderObj->id_address_invoice);
} else {
    $addressObj = new Address($orderObj->id_address_delivery);
}

$stateObj   = new StateCore($addressObj->id_state);
$file       = _PS_MODULE_DIR_ . "mpgoogleaddress/pdf/label.pdf";
if($PageWidth<10){$PageWidth=100;}
if($PageHeight<10){$PageHeight=100;}
$pageSize   = [$PageWidth,$PageHeight];
$pdf        = new TCPDF("P", "mm", $pageSize, true, "UTF-8", false, false);

$pdf->SetAutoPageBreak(false);
$pdf->setCellHeightRatio(0.5);
$pdf->AddPage();
$pdf->setCellMargins(0, 5, 0, 5);
$pdf->setCellPaddings(5, 5, 5, 5);

//LABEL
if(!empty($ShowLogo) || $ShowLogo==true)
{
    $prop = $imageSize[0]/$imageSize[1]; // [0]=Width, [1]=Height
    $w = $PageWidth*90/100;
    $h = $w/$prop;
    $pdf->image($Logo,$PageWidth*5/100,5,$w);
    $pdf->SetY($pdf->GetY() + $h - 5);
}

if(1==2)
{
    print "<pre>";
    print "\nwidth=".$PageWidth;
    print "\nheight=".$PageHeight;
    print "\nlogo=".$ShowLogo;
    print "\nlogoext=".$LogoExt;
    print "\nfile=".$Logo;
    print "\nphone=".$ShowPhone;
    print "\nmobile=".$ShowMobile;
    print "\norder=".$ShowOrder;
    print "\nimg_width=".$imageSize[0];
    print "\nimg_height=".$imageSize[1];
    print "\nprop=".$prop;
    print "\nprint_img_width=".$w;
    print "\nprint_img_height=".$h;
    print "\nPS_BASE_URL: ". $_PS_BASE_URL_;
    print "<pre>";
}
//DEST
$posY = $pdf->GetY();
$pdf->SetFont("helvetica", "B", "10");
$pdf->Cell(100, 0, "DEST", 0, 0, "L", false, "", 1, false, "C","C");
$pdf->ln(6);
$pdf->SetFont("helvetica", "B", "18");
if(!empty($addressObj->company))
{
    //COMPANY
    $pdf->SetFont("helvetica", "B", "14");
    $pdf->Cell(100, 5, strtoupper($addressObj->company), 0, 0, "L", false, "", 1, false, "C","C");
    $pdf->ln(6);
    //NAME
    $pdf->SetFont("helvetica", "B", "10");
    $pdf->Cell(100, 5, strtoupper($addressObj->firstname . " " . $addressObj->lastname), 0, 0, "L", false, "", 1, false, "C","C");
    $pdf->ln(6);
}
else
{
    //NAME
    $pdf->SetFont("helvetica", "B", "18");
    $pdf->Cell(100, 5, strtoupper($addressObj->firstname . " " . $addressObj->lastname), 0, 0, "L", false, "", 1, false, "C","C");
    $pdf->ln(6);
}

//ADDRESS
$pdf->SetFont("helvetica", "", "14");
$pdf->Cell(100, 5, $addressObj->address1, 0, 0, "L", false, "", 1, false, "C","C");
$pdf->ln(6);
if(!empty($addressObj->address2))
{
    $pdf->Cell(100, 5, $addressObj->address2, 0, 0, "L", false, "", 1, false, "C","C");
    $pdf->ln(6);
}
//POSTCODE
$pdf->Cell(100, 5, $addressObj->postcode . " " . $addressObj->city, 0, 0, "L", false, "", 1, false, "C","C");
$pdf->ln(6);
//STATE
$pdf->Cell(100, 5, $stateObj->name . " " . $stateObj->iso_code, 0, 0, "L", false, "", 1, false, "C","C");
$pdf->ln(6);

if($ShowPhone || $ShowMobile || $ShowOrder)
{
    $pdf->Line(10, $pdf->GetY()+3.5, $PageWidth-10, $pdf->GetY()+3.5);
    $pdf->ln(4);
}

if($ShowPhone && $ShowMobile)
{
    $pdf->SetFontSize(12);
    if($addressObj->phone==$addressObj->phone_mobile)
    {
        $pdf->Cell(100, 5, "TEL: " . $addressObj->phone, 0, 0, "L", false, "", 1, false, "C","C");
    }
    else
    {
        $pdf->Cell(100, 5, "TEL: " . $addressObj->phone . ", CELL: " . $addressObj->phone_mobile, 0, 0, "L", false, "", 1, false, "C","C");
    }
}
else if($ShowPhone)
{
    $pdf->SetFontSize(12);
    $pdf->Cell(100, 5, "TEL: " . $addressObj->phone, 0, 0, "L", false, "", 1, false, "C","C");
    $pdf->ln(4);
}
else if($ShowMobile)
{
    $pdf->SetFontSize(12);
    $pdf->Cell(100, 5, "CELL: " . $addressObj->phone_mobile, 0, 0, "L", false, "", 0, false, "C","C");
    $pdf->ln(4);
}

if($ShowOrder)
{
    //$pdf->sety($PageHeight-10);
    $pdf->SetY($posY);
    $pdf->SetFont("helvetica", "B", "10");
    $pdf->Cell(90, 5, "ORDINE: " . $orderObj->reference, 0, 0, "R", false, "", 0, false, "C","C");
}

$pdf->lastPage();
$pdf->Output($file,"F");
chmod($file, 0775);

$response = ['url' => "../modules/mpgoogleaddress/pdf/label.pdf"];

print Tools::jsonEncode($response);