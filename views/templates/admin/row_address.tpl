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

<div class="row" style="font-size: 10pt !important;">
    <div class=col-md-8>
    {if isset($address->company) && !empty($address->company)}
        <div class="row">
            <i class='icon icon-home i-col'></i><div class="div-col"><strong>{{$address->company|upper}|escape:'htmlall':'UTF-8'}</strong></div>
        </div>
    {/if}
        <div class="row">
            <i class='icon icon-user'></i><div class="div-col">{$address->firstname|escape:'htmlall':'UTF-8'} {$address->lastname|escape:'htmlall':'UTF-8'}</div>
        </div>
        <div class="row">
            <i class='icon icon-map-marker color-blue'></i><div class="div-col">{$address->address1|escape:'htmlall':'UTF-8'}</div>
        </div>
    {if isset($address->address2) && !empty($address->address2)}
        <div class="row">
            <i class='icon icon-map-marker color-blue'></i><div class="div-col">{$address->address2|escape:'htmlall':'UTF-8'}</div>
        </div>
    {/if}
        <div class="row">
            <i class='icon icon-map-marker color-blue'></i><div class="div-col">{$address->postcode|escape:'htmlall':'UTF-8'} - {$address->city|escape:'htmlall':'UTF-8'}</div>
        </div>
        <div class="row">
            <i class='icon icon-map-marker color-blue'></i><div class="div-col">{$address->state->name|escape:'htmlall':'UTF-8'} <strong>{{$address->state->iso_code|upper}|escape:'htmlall':'UTF-8'}</strong></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            
        </div>
        <div class="row">
            <span class="badge">
            <i class='icon icon-phone color-white'></i><div class="div-col"><strong>{$address->phone|escape:'htmlall':'UTF-8'}</strong></div>
            </span>
        </div>
        <div class="row">
            <hr>
        </div>
        <div class="row">
            <span class="badge badge-success">
            <i class='icon icon-phone color-yellow'></i><div class="div-col"><strong>{$address->phone_mobile|escape:'htmlall':'UTF-8'}</strong></div>
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
                    {$address->vat_number|escape:'htmlall':'UTF-8'}
                </a>
            </strong>
        </div>
    {/if}
    {if isset($address->dni) && !empty($address->dni)}
        <div class="row">
            <strong>
                <i class='icon icon-user color-red'></i>
                {{$address->dni|upper}|escape:'htmlall':'UTF-8'}
            </strong>
        </div>
    {/if}
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-12">
        <i class='icon icon-comment'></i><div class="div-col">{$address->other|escape:'htmlall':'UTF-8'}</div>
    </div>
</div>
