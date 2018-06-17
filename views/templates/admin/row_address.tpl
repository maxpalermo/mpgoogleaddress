{*
* 2017 mpSOFT
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
*  @author    mpSOFT <info@mpsoft.it>
*  @copyright 2017 mpSOFT Massimiliano Palermo
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of mpSOFT
*}

<div class="row">
    <div class=col-md-6>
    {if isset($address->company) && !empty($address->company)}
        <div class="row">
            <i class='icon icon-home i-col'></i><div class="div-col"><strong>{$address->company|upper}</strong></div>
        </div>
    {/if}
        <div class="row">
            <i class='icon icon-user'></i><div class="div-col">{$address->firstname} {$address->lastname}</div>
        </div>
        <div class="row">
            <i class='icon icon-map-marker color-blue'></i><div class="div-col">{$address->address1}</div>
        </div>
    {if isset($address->address2) && !empty($address->address2)}
        <div class="row">
            <i class='icon icon-map-marker color-blue'></i><div class="div-col">{$address->address2}</div>
        </div>
    {/if}
        <div class="row">
            <i class='icon icon-map-marker color-blue'></i><div class="div-col">{$address->postcode} - {$address->city}</div>
        </div>
        <div class="row">
            <i class='icon icon-map-marker color-blue'></i><div class="div-col">{$address->state->name} <strong>{$address->state->iso_code|upper}</strong></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            
        </div>
        <div class="row">
            <span class="badge">
            <i class='icon icon-phone color-white'></i><div class="div-col"><strong>{$address->phone}</strong></div>
            </span>
        </div>
        <div class="row">
            <hr>
        </div>
        <div class="row">
            <span class="badge badge-success">
            <i class='icon icon-phone color-yellow'></i><div class="div-col"><strong>{$address->phone_mobile}</strong></div>
            </span>
        </div>
        <div class="row">
            <hr>
        </div>
    {if isset($address->vat_number) && !empty($address->vat_number)}
        <div class="row">
            <strong>
                <i class='icon icon-credit-card color-red'></i>
                <a id="vat_number_link" href="javascript:void(0);" onclick="verifyVAT();">
                    {$address->vat_number}
                </a>
            </strong>
        </div>
    {/if}
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-12">
        <i class='icon icon-comment'></i><div class="div-col">{$address->other}</div>
    </div>
</div>
