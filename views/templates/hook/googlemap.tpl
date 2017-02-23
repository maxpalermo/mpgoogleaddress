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
<input type="hidden" id="address_id_order" value="{$address_id_order|escape:'htmlall':'UTF-8'}">
<div id="addressShippingData">
    <div>
        <div style="float: left;" id="addressShippingCustomer">
            <img src='../modules/mpgoogleaddress/views/img/user.png'>
            <strong>{{$address_delivery->firstname|upper}|escape:'htmlall':'UTF-8'} {{$address_delivery->lastname|upper}|escape:'htmlall':'UTF-8'}</strong><br>
            {if $address_delivery->company}
                <img src='../modules/mpgoogleaddress/views/img/dot.png'>
                <strong><i>{{$address_delivery->company|upper}|escape:'htmlall':'UTF-8'}</i></strong><br>
            {/if}
            <img src='../modules/mpgoogleaddress/views/img/dot.png'>{$address_delivery->address1|escape:'htmlall':'UTF-8'}<br>
            {if !empty($address_delivery->address2)}
                <img src='../modules/mpgoogleaddress/views/img/dot.png'>
                {$address_delivery->address2|escape:'htmlall':'UTF-8'}<br>
            {/if}
            <img src='../modules/mpgoogleaddress/views/img/dot.png'>{$address_delivery->postcode|escape:'htmlall':'UTF-8'} - {$address_delivery->city|escape:'htmlall':'UTF-8'}<br>
            <img src='../modules/mpgoogleaddress/views/img/dot.png'>{{$state_delivery->name|upper}|escape:'htmlall':'UTF-8'}<br>
            <img src='../modules/mpgoogleaddress/views/img/dot.png'>{$address_delivery->country|escape:'htmlall':'UTF-8'}<br>
            {if !empty($address_delivery->phone)}<img src='../modules/mpgoogleaddress/views/img/phone.png'> {$address_delivery->phone|escape:'htmlall':'UTF-8'}<br>{/if}
            {if !empty($address_delivery->phone_mobile)}<img src='../modules/mpgoogleaddress/views/img/mobile.png'> {$address_delivery->phone_mobile|escape:'htmlall':'UTF-8'}<br>{/if}
        </div>
        <div style="float: right;" id="addressShippingButtons">
            
        </div>
    </div>
    <br style='clear: both;'>
    <div id="shipMap" style="text-align: center;">
        
    </div>
</div>

<div id="addressInvoiceData">
    <div>
        <div style="float: left;" id="addressInvoiceCustomer">
            <img src='../modules/mpgoogleaddress/views/img/user.png'>
            <strong>{{$address_invoice->firstname|upper}|escape:'htmlall':'UTF-8'} {{$address_invoice->lastname|upper}|escape:'htmlall':'UTF-8'}</strong><br>
            {if $address_invoice->company}
                <img src='../modules/mpgoogleaddress/views/img/dot.png'>
                <strong><i>{{$address_invoice->company|upper}|escape:'htmlall':'UTF-8'}</i></strong><br>
            {/if}
            <img src='../modules/mpgoogleaddress/views/img/dot.png'>{$address_invoice->address1|escape:'htmlall':'UTF-8'}<br>
            {if !empty($address_invoice->address2)}
                <img src='../modules/mpgoogleaddress/views/img/dot.png'>
                {$address_invoice->address2|escape:'htmlall':'UTF-8'}<br>
            {/if}
            <img src='../modules/mpgoogleaddress/views/img/dot.png'>{$address_invoice->postcode|escape:'htmlall':'UTF-8'} - {$address_invoice->city|escape:'htmlall':'UTF-8'}<br>
            <img src='../modules/mpgoogleaddress/views/img/dot.png'>{{$state_invoice->name|upper}|escape:'htmlall':'UTF-8'}<br>
            <img src='../modules/mpgoogleaddress/views/img/dot.png'>{$address_invoice->country|escape:'htmlall':'UTF-8'}<br>
            {if !empty($address_invoice->phone)}<img src='../modules/mpgoogleaddress/views/img/phone.png'> {$address_invoice->phone|escape:'htmlall':'UTF-8'}<br>{/if}
            {if !empty($address_invoice->phone_mobile)}<img src='../modules/mpgoogleaddress/views/img/mobile.png'> {$address_invoice->phone_mobile|escape:'htmlall':'UTF-8'}<br>{/if}
            {if !empty($address_invoice->dni)}<img src='../modules/mpgoogleaddress/views/img/dni.png'><strong>{{$address_invoice->dni|upper}|escape:'htmlall':'UTF-8'}</strong><br>{/if}
            {if !empty($address_invoice->vat_number)}<img src='../modules/mpgoogleaddress/views/img/vat.png'><strong>{$address_invoice->vat_number|escape:'htmlall':'UTF-8'}</strong><br>{/if}
        </div>
        <div style="float: right;" id="addressInvoiceButtons">
            
        </div>
    </div>
    <br style='clear: both;'>
    <div id="invoMap" style="text-align: center;">
        
    </div>
</div>

{if $showmap}
<iframe id='googlemap_delivery' frameborder="0" style="border:0; margin: 0 auto; margin-top: 10px;"
    src="https://www.google.com/maps/embed/v1/place?key={$api_key|escape:'htmlall':'UTF-8'}
        &amp;q={$address_delivery->address1|escape:'htmlall':'UTF-8'}
        +{$address_delivery->postcode|escape:'htmlall':'UTF-8'}
        +{$address_delivery->city|escape:'htmlall':'UTF-8'}
        +{$state_delivery->name|escape:'htmlall':'UTF-8'}
        +{$address_delivery->country|escape:'htmlall':'UTF-8'}" allowfullscreen="">                                                   
</iframe>

<iframe id='googlemap_invoice' frameborder="0" style="border:0; margin: 0 auto; margin-top: 10px;"
    src="https://www.google.com/maps/embed/v1/place?key={$api_key|escape:'htmlall':'UTF-8'}
        &amp;q={$address_delivery->address1|escape:'htmlall':'UTF-8'}
        +{$address_delivery->postcode|escape:'htmlall':'UTF-8'}
        +{$address_delivery->city|escape:'htmlall':'UTF-8'}
        +{$state_delivery->name|escape:'htmlall':'UTF-8'}
        +{$address_delivery->country|escape:'htmlall':'UTF-8'}" allowfullscreen="">                                                   
</iframe>
{/if}

{if $printlabel}
<a class="btn btn-default pull-right" href="return false;" id='printLabelShipping'>
    <i class="icon-print"></i> {l s='Print label' mod='mpgoogleaddress'}
</a>
<a class="btn btn-default pull-right" href="return false;" id='printLabelInvoice'>
    <i class="icon-print"></i> {l s='Print label' mod='mpgoogleaddress'}
</a>
    
{/if}

<script type="text/javascript">
    $(document).ready(function()
    {
        /**
         * 
         * get all buttons
         */
        var shipBtn = new Array();
        $('#addressShipping div.well .btn').each(function(){
            shipBtn.push($(this));
        });
        
        var invoBtn = new Array();
        $('#addressInvoice div.well .btn').each(function(){
            invoBtn.push($(this));
        });
        /**
         * Remove old shipping panel
         */
        $("#addressShipping .well").hide();
        /**
         * Add new shipping panel
         */
        for(var i=0; i<shipBtn.length; i++)
        {
            $(shipBtn[i]).appendTo("#addressShippingButtons");
            $("<br><br>").appendTo("#addressShippingButtons");
        }
        $("#printLabelShipping").detach().appendTo("#addressShippingButtons");
        $("<br><br>").appendTo("#addressShippingButtons");
        $("#googlemap_delivery").detach().appendTo("#shipMap");
        $("#addressShippingData").detach().appendTo("#addressShipping");
        /**
         * Remove old invoice panel
         */
        $("#addressInvoice .well").hide();
        /**
         * Add new invoice panel
         */
        for(var i=0; i<invoBtn.length; i++)
        {
            $(invoBtn[i]).appendTo("#addressInvoiceButtons");
            $("<br><br>").appendTo("#addressInvoiceButtons");
        }
        $("#printLabelInvoice").detach().appendTo("#addressInvoiceButtons");
        $("#googlemap_invoice").detach().appendTo("#invoMap");
        $("#addressInvoiceData").detach().appendTo("#addressInvoice");      
        
        $("#printLabelShipping").on("click",function(e){
            e.preventDefault();
            var url = "{$http|escape:'htmlall':'UTF-8'}{$host|escape:'htmlall':'UTF-8'}{$base|escape:'htmlall':'UTF-8'}/modules/mpgoogleaddress/views/ajax/createLabel.php";
            var data = {
                        'id_order': $('#address_id_order').val(),
                        'address_type': 'shipping'
                       };
            $.getJSON(url,data,function(response){
                window.open(response.url);
            });
        });
        
        $("#printLabelInvoice").on("click",function(e){
            e.preventDefault();
            e.preventDefault();
            var url = "{$http|escape:'htmlall':'UTF-8'}{$host|escape:'htmlall':'UTF-8'}{$base|escape:'htmlall':'UTF-8'}/modules/mpgoogleaddress/views/ajax/createLabel.php";
            var data = {
                        'id_order': $('#address_id_order').val(),
                        'address_type': 'invoice'
                       };
            $.getJSON(url,data,function(response){
                window.open(response.url);
            });
        });
    });
</script>