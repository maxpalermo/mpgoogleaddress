{*
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
*  @author    mpsoft - Massimiliano Palermo <info@mpsoft.it>
*  @copyright 2017 mpsoft®
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of mpsoft® Massimiliano Palermo
*}

<style>
    #addressShippingData br, #addressInvoiceData br
    {
        min-height: 1.5em;
    }
    #addressShippingData img, #addressInvoiceData img
    {
        margin-right: 10px;
        display: inline-block;
    }
</style>

<div id="addressShippingData">
    <table>
        <tbody>
            <tr>
                <td>                  
                    <img src='{$img|escape:'htmlall':'UTF-8'}user.png'>
                </td>
                <td>
                    <strong>
                        {{$address_delivery->firstname|upper}|escape:'htmlall':'UTF-8'} 
                        {{$address_delivery->lastname|upper}|escape:'htmlall':'UTF-8'}
                    </strong>
                </td>
            </tr>
            {if $address_delivery->company}
            <tr>
                <td>
                    <img src='{$img|escape:'htmlall':'UTF-8'}/dot.png'>
                </td>
                <td>
                    <strong><i>{{$address_delivery->company|upper}|escape:'htmlall':'UTF-8'}</i></strong>
                </td>
            </tr>
            {/if}
            <tr>
                <td>
                    <img src='{$img|escape:'htmlall':'UTF-8'}dot.png'>
                </td>
                <td>
                    {$address_delivery->address1|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            {if !empty($address_delivery->address2)}
            <tr>
                <td>
                    <img src='{$img|escape:'htmlall':'UTF-8'}dot.png'>
                </td>
                <td>
                    {$address_delivery->address2|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            {/if}
            <tr>
                <td>
                   <img src='{$img|escape:'htmlall':'UTF-8'}dot.png'> 
                </td>
                <td>
                    {$address_delivery->postcode|escape:'htmlall':'UTF-8'} - {$address_delivery->city|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            <tr>
                <td>
                  <img src='{$img|escape:'htmlall':'UTF-8'}dot.png'>  
                </td>
                <td>
                    {{$state_delivery->name|upper}|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            <tr>
                <td>
                    <img src='{$img|escape:'htmlall':'UTF-8'}dot.png'>
                </td>
                <td>
                    {$address_delivery->country|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            {if !empty($address_delivery->phone)}
            <tr>
                <td>
                    <img src='{$img|escape:'htmlall':'UTF-8'}phone.png'>
                </td>
                <td>
                    {$address_delivery->phone|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            {/if}
            {if !empty($address_delivery->phone_mobile)}
            <tr>
                <td>
                   <img src='{$img|escape:'htmlall':'UTF-8'}mobile.png'> 
                </td>
                <td>
                    {$address_delivery->phone_mobile|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            {/if}
        </tbody>
    </table>
</div>

<div id="addressInvoiceData">
    <table>
        <tbody>
            <tr>
                <td>
                    <img src='{$img|escape:'htmlall':'UTF-8'}user.png'>
                </td>
                <td>
                    <strong>
                        {{$address_invoice->firstname|upper}|escape:'htmlall':'UTF-8'} 
                        {{$address_invoice->lastname|upper}|escape:'htmlall':'UTF-8'}
                    </strong>
                </td>
            </tr>
            {if $address_invoice->company}
            <tr>
                <td>
                    <img src='{$img|escape:'htmlall':'UTF-8'}dot.png'>
                </td>
                <td>
                    <strong><i>{{$address_invoice->company|upper}|escape:'htmlall':'UTF-8'}</i></strong>
                </td>
            </tr>
            {/if}
            <tr>
                <td>
                    <img src='{$img|escape:'htmlall':'UTF-8'}dot.png'>
                </td>
                <td>
                    {$address_invoice->address1|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            {if !empty($address_invoice->address2)}
            <tr>
                <td>
                    <img src='{$img|escape:'htmlall':'UTF-8'}dot.png'>
                </td>
                <td>
                    {$address_invoice->address2|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            {/if}
            <tr>
                <td>
                   <img src='{$img|escape:'htmlall':'UTF-8'}dot.png'> 
                </td>
                <td>
                    {$address_invoice->postcode|escape:'htmlall':'UTF-8'} - {$address_invoice->city|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            <tr>
                <td>
                  <img src='{$img|escape:'htmlall':'UTF-8'}dot.png'>  
                </td>
                <td>
                    {{$state_invoice->name|upper}|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            <tr>
                <td>
                    <img src='{$img|escape:'htmlall':'UTF-8'}dot.png'>
                </td>
                <td>
                    {$address_invoice->country|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            {if !empty($address_invoice->phone)}
            <tr>
                <td>
                    <img src='{$img|escape:'htmlall':'UTF-8'}phone.png'>
                </td>
                <td>
                    {$address_invoice->phone|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            {/if}
            {if !empty($address_invoice->phone_mobile)}
            <tr>
                <td>
                   <img src='{$img|escape:'htmlall':'UTF-8'}mobile.png'> 
                </td>
                <td>
                    {$address_invoice->phone_mobile|escape:'htmlall':'UTF-8'}
                </td>
            </tr>
            {/if}
            {if !empty($address_invoice->dni)}
            <tr>
                <td>
                    <img src='{$img|escape:'htmlall':'UTF-8'}dni.png'>
                </td>
                <td>
                    <strong>{{$address_invoice->dni|upper}|escape:'htmlall':'UTF-8'}</strong>
                </td>
            </tr>
            {/if}
            {if !empty($address_invoice->vat_number)}
            <tr>
                <td>
                    <img src='{$img|escape:'htmlall':'UTF-8'}vat.png'>
                </td>
                <td>
                    <strong>{$address_invoice->vat_number|escape:'htmlall':'UTF-8'}</strong>
                </td>
            </tr>
            {/if}
        </tbody>
    </table>
</div>

<iframe id='googlemap_delivery' frameborder="0" style="border:0"
    src="https://www.google.com/maps/embed/v1/place?key={$api_key|escape:'htmlall':'UTF-8'}
    &amp;q={$address_delivery->address1|escape:'htmlall':'UTF-8'}
    +{$address_delivery->postcode|escape:'htmlall':'UTF-8'}
    +{$address_delivery->city|escape:'htmlall':'UTF-8'}
    +{$state_delivery->name|escape:'htmlall':'UTF-8'}
    +{$address_delivery->country|escape:'htmlall':'UTF-8'}" allowfullscreen="">                                                   
</iframe>

<iframe id='googlemap_invoice' frameborder="0" style="border:0"
    src="https://www.google.com/maps/embed/v1/place?key={$api_key|escape:'htmlall':'UTF-8'}
    &amp;q={$address_delivery->address1|escape:'htmlall':'UTF-8'}
    +{$address_delivery->postcode|escape:'htmlall':'UTF-8'}
    +{$address_delivery->city|escape:'htmlall':'UTF-8'}
    +{$state_delivery->name|escape:'htmlall':'UTF-8'}
    +{$address_delivery->country|escape:'htmlall':'UTF-8'}" allowfullscreen="">                                                   
</iframe>
    
<script type="text/javascript">
    $(document).ready(function()
    {
        $("#addressShipping .well .row .col-sm-6:nth-child(1)>div").remove();
        $("#addressShippingData").detach().prependTo("#addressShipping .well .row .col-sm-6:nth-child(1)");
        
        $("#addressInvoice .well .row .col-sm-6:nth-child(1)>div").remove();
        $("#addressInvoiceData").detach().prependTo("#addressInvoice .well .row .col-sm-6:nth-child(1)");
        
        $("#map-delivery-canvas").html('');
        $("#googlemap_delivery").detach().appendTo("#map-delivery-canvas");
        
        $("#map-invoice-canvas").html('');
        $("#googlemap_invoice").detach().appendTo("#map-invoice-canvas");
        
    });
</script>