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

<table style="font-size: 12pt;">
    <tbody>
        <tr>
            <td>
                {if !empty($logo)}
                <img src="{$logo}">
                {/if}
            </td>
        </tr>
        <tr>
            <td style="text-align: right;">{l s='Order' mod='mpgoogleaddress'} {$reference|upper}</td>
        </tr>
        <tr>
            <td style="font-size: 14pt;">{$name|upper}</td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
        <tr>
            <td>{$address1}</td>
        </tr>
        {if !empty($address2)}
        <tr>
            <td>{$address2}</td>
        </tr>
        {/if}
        <tr>
            <td>{$postcode} {$city}</td>
        </tr>
        <tr>
            <td>{$state|upper} <strong>{$iso_code|upper}</strong></td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
        {if !empty($phone) && !empty($phone_mobile)}
        <tr><td>{l s='Phone' mod='mpgoogleaddress'} {$phone_mobile}</td></tr>
        {else if !empty($phone)}
        <tr><td>{l s='Phone' mod='mpgoogleaddress'} {$phone}</td></tr>
        {else if !empty($phone_mobile)}
        <tr><td>{l s='Phone' mod='mpgoogleaddress'} {$phone_mobile}</td></tr>
        {/if}
    </tbody>    
</table>