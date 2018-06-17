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
    .spc
    {
        margin-right: 10px;
        margin-bottom: 10px;
    }
</style>
<input type="hidden" id="address_id_order" value="{$address_id_order|escape:'htmlall':'UTF-8'}">
<div id="addressShippingData">
    <div>
        <div style="float: left;" id="addressShippingCustomer">
            <i class="icon spc fa-user"></i>
            <strong>{{$address_delivery->firstname|upper}|escape:'htmlall':'UTF-8'} {{$address_delivery->lastname|upper}|escape:'htmlall':'UTF-8'}</strong><br>
            
            {if $address_delivery->company}
                <i class="icon spc fa-building"></i>
                <strong>{{$address_delivery->company|upper}|escape:'htmlall':'UTF-8'}</strong><br>
            {/if}
            
            <i class="icon spc fa-map-marker"></i>
            {$address_delivery->address1|escape:'htmlall':'UTF-8'}<br>
            
            {if !empty($address_delivery->address2)}
                <i class="icon spc fa-map-marker"></i>
                {$address_delivery->address2|escape:'htmlall':'UTF-8'}<br>
            {/if}
            
            <i class="icon spc fa-map-marker"></i>
            {$address_delivery->postcode|escape:'htmlall':'UTF-8'} - {$address_delivery->city|escape:'htmlall':'UTF-8'}<br>
            
            <i class="icon spc fa-map-marker"></i>
            {{$state_delivery->name|upper}|escape:'htmlall':'UTF-8'} <strong>({{$state_delivery->iso_code|upper}|escape:'htmlall':'UTF-8'})</strong><br>
            
            <i class="icon spc fa-map-marker"></i>
            {$address_delivery->country|escape:'htmlall':'UTF-8'}<br>
            
            {if !empty($address_delivery->phone)}
                <i class="icon spc fa-phone"></i>
                {$address_delivery->phone|escape:'htmlall':'UTF-8'}<br>
            {/if}
            
            {if !empty($address_delivery->phone_mobile)}
                <i class="icon spc fa-phone"></i>
                {$address_delivery->phone_mobile|escape:'htmlall':'UTF-8'}<br>
            {/if}
            
            {if !empty($address_delivery->other)}
                <i class="icon spc fa-comment"></i>
                {$address_delivery->other|escape:'htmlall':'UTF-8'}<br>
            {/if}
        </div>
        <div style="float: right;" id="addressShippingButtons">
            <a class="btn btn-default pull-right" href="return false;" id='removeDniShipping'>
                <i class="icon-times" style="color: #BB7979;"></i> {l s='Remove DNI' mod='mpgoogleaddress'}
            </a>
            <br>
            <a class="btn btn-default pull-right" href="return false;" id='removeVatShipping'>
                <i class="icon-times" style="color: #BB7979;"></i> {l s='Remove VAT' mod='mpgoogleaddress'}
            </a>
            <br>
        </div>
    </div>
    <br style='clear: both;'>
    <div id="shipMap" style="text-align: center;">
        
    </div>
</div>

<div id="addressInvoiceData">
    <div>
        <div style="float: left;" id="addressInvoiceCustomer">
            <i class="icon spc fa-user"></i>
            <strong>{{$address_invoice->firstname|upper}|escape:'htmlall':'UTF-8'} {{$address_invoice->lastname|upper}|escape:'htmlall':'UTF-8'}</strong><br>
            
            {if $address_invoice->company}
                <i class="icon spc fa-building"></i>
                <strong>{{$address_invoice->company|upper}|escape:'htmlall':'UTF-8'}</strong><br>
            {/if}
            
            <i class="icon spc fa-map-marker"></i>
            {$address_invoice->address1|escape:'htmlall':'UTF-8'}<br>
            
            {if !empty($address_invoice->address2)}
                <i class="icon spc fa-map-marker"></i>
                {$address_invoice->address2|escape:'htmlall':'UTF-8'}<br>
            {/if}
            
            <i class="icon spc fa-map-marker"></i>
            {$address_invoice->postcode|escape:'htmlall':'UTF-8'} - {$address_invoice->city|escape:'htmlall':'UTF-8'}<br>
            
            <i class="icon spc fa-map-marker"></i>
            {{$state_invoice->name|upper}|escape:'htmlall':'UTF-8'} <strong>({{$state_invoice->iso_code|upper}|escape:'htmlall':'UTF-8'})</strong><br>
            
            <i class="icon spc fa-map-marker"></i>
            {$address_invoice->country|escape:'htmlall':'UTF-8'}<br>
            
            {if !empty($address_invoice->phone)}
                <i class="icon spc fa-phone"></i>
                {$address_invoice->phone|escape:'htmlall':'UTF-8'}<br>
            {/if}
            
            {if !empty($address_invoice->phone_mobile)}
                <i class="icon spc fa-phone"></i>
                {$address_invoice->phone_mobile|escape:'htmlall':'UTF-8'}<br>
            {/if}
            
            {if !empty($address_invoice->other)}
                <i class="icon spc fa-comment"></i>
                {$address_invoice->other|escape:'htmlall':'UTF-8'}<br>
            {/if}
            {if !empty($address_invoice->dni)}
                <i class="icon spc fa-vcard-o"></i>
                <strong>{{$address_invoice->dni|upper}|escape:'htmlall':'UTF-8'}</strong><br>
            {/if}
            {if !empty($address_invoice->vat_number)}
                <i class="icon spc fa-vcard-o"></i>
                <strong>{$address_invoice->vat_number|escape:'htmlall':'UTF-8'}</strong><br>
            {/if}
        </div>
        <div style="float: right;" id="addressInvoiceButtons">
            <a class="btn btn-default pull-right" href="return false;" id='removeDniInvoice'>
                <i class="icon-times" style="color: #BB7979;"></i> {l s='Remove DNI' mod='mpgoogleaddress'}
            </a>
            <br>
            <a class="btn btn-default pull-right" href="return false;" id='removeVatInvoice'>
                <i class="icon-times" style="color: #BB7979;"></i> {l s='Remove VAT' mod='mpgoogleaddress'}
            </a>
            <br>
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
        &amp;q={$address_invoice->address1|escape:'htmlall':'UTF-8'}
        +{$address_invoice->postcode|escape:'htmlall':'UTF-8'}
        +{$address_invoice->city|escape:'htmlall':'UTF-8'}
        +{$state_invoice->name|escape:'htmlall':'UTF-8'}
        +{$address_invoice->country|escape:'htmlall':'UTF-8'}" allowfullscreen="">                                                   
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
            printLabelAddress('shipping');
        });
        
        $("#printLabelInvoice").on("click",function(e){
            e.preventDefault();
            printLabelAddress('invoice');
        });
        $('#removeDniShipping').on('click', function(event){
            event.preventDefault();
            jConfirm("{l s='Remove Dni from delivery address?' mod='mpgoogleaddress'}", '{l s='Remove DNI' mod='mpgoogleaddress'}', function(r){
                console.log('response', r);
                if(r) {
                   removeDataFromAddress('dni', 'delivery');
                }
            });
        });
        $('#removeVatShipping').on('click', function(event){
            event.preventDefault();
            jConfirm("{l s='Remove Vat number from delivery address?' mod='mpgoogleaddress'}", '{l s='Remove VAT' mod='mpgoogleaddress'}', function(r){
                console.log('response', r);
                if(r) {
                   removeDataFromAddress('vat_number', 'delivery');
                }
            });
        });
        $('#removeDniInvoice').on('click', function(event){
            event.preventDefault();
            jConfirm("{l s='Remove Dni from invoice address?' mod='mpgoogleaddress'}", '{l s='Remove DNI' mod='mpgoogleaddress'}', function(r){
                console.log('response', r);
                if(r) {
                   removeDataFromAddress('dni', 'invoice');
                }
            });
        });
        $('#removeVatInvoice').on('click', function(event){
            event.preventDefault();
            jConfirm("{l s='Remove Vat number from invoice address?' mod='mpgoogleaddress'}", '{l s='Remove VAT' mod='mpgoogleaddress'}', function(r){
                console.log('response', r);
                if(r) {
                   removeDataFromAddress('vat_number', 'invoice');
                }
            });
        });
    });
    function removeDataFromAddress(fieldname, address_type)
    {
        console.log('RemoveDataFromAddress:',fieldname,address_type);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '{$ajax_url|escape:'htmlall':'UTF-8'}',
            useDefaultXhrHeader: false,
            data: 
            {
                token: '{$token|escape:'htmlall':'UTF-8'}',
                ajax: true,
                action: 'removeDataFromAddress',
                id_order: $('#address_id_order').val(),
                fieldname: fieldname,
                address: address_type
            }
        })
        .done(function(result){
            if (result.error) {
                $.growl.error({ message: result.message });
            } else {
                $.growl.notice({ message: result.message });
                window.location.reload();
            }
        })
        .fail(function(){
            jAlert('{l s='Error during Ajax call' mod='mpgoogleaddress'}');
        });
    }
    
    function printLabelAddress(type)
    {
        $.ajax({
            type: 'POST',
            //dataType: 'json',
            url: '{$ajax_url|escape:'htmlall':'UTF-8'}',
            //useDefaultXhrHeader: false,
            responseType: 'arraybuffer',
            processData: true,
            data: 
            {
                token: '{$token|escape:'htmlall':'UTF-8'}',
                ajax: true,
                action: 'CreateLabel',
                id_order: $('#address_id_order').val(),
                address_type: type
            }
        })
        .done(function(result){
            window.open(result);
        })
        .fail(function(result){
            jAlert('fail');
        });
    }
</script>
