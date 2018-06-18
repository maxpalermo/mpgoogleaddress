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
    .color-red
    {
        color: #BB7979 !important;
    }
    .color-green
    {
        color: #4eb357 !important;
    }
    .color-orange
    {
        color: #fbbb22 !important;
    }
    .color-blue
    {
        color: #25b9d7 !important;
    }
    .color-white
    {
        color: #fefefe;
    }
    .map-embedded
    {
        width: 100%;
        height: 100%;
    }
    .div-col
    {
        display: inline-block !important;
        padding-left: 6px !important;
    }
</style>
<div class="row" id="ps_addresses">
    <div class="row">
        <div class="col-md-9">
            <select id="change_delivery_address" class="input" style="width: 100%;">
                {foreach $addresses as $addr}
                    <option value="{$addr->id}">
                        {$addr->address1}, {$addr->postcode} {$addr->city} {$addr->state->iso_code}
                        {if isset($addr->vat_number) && !empty($addr->vat_number)}
                            &nbsp;({l s='Vat numb' mod='mpgoogleaddress'} {$addr->vat_number})
                        {/if}
                    </option>
                {/foreach}
            </select>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-info" onclick="javascript:changeAddress();">
                <i class="icon icon-refresh"></i>
                &nbsp
                {l s='Change' mod='mpgoogleaddress'}
            </button>
        </div>
    </div>
    <br>
    <!--Nav content-->
    <ul class='nav nav-tabs' id="tabAddresses">
        <li class="active" data-type="delivery" id_address='{$address_delivery->id}'>
            <a href="#addressShipping" onclick='javascript:activatePane();'>
                <i class="icon icon-truck"></i>&nbsp;{l s='Delivery address' mod='mpgoogleaddress'}
            </a>
        </li>
        <li data-type="invoice" id_address='{$address_invoice->id}'>
            <a href="#addressInvoice" onclick='javascript:activatePane();'>
                <i class="icon icon-list"></i>&nbsp;{l s='Invoice address' mod='mpgoogleaddress'}
            </a>
        </li>
    </ul>
    <!--Tab content-->
    <div class="tab-content panel">
        <!--Tabs-->
        <div class="tab-pane active" id="addressShipping">
        
            <br>
            {assign var="address" value=$address_delivery}
            {include file=$row_address}
        </div>
        <div class="tab-pane" id="addressInvoice">
            {assign var="address" value=$address_invoice}
            {assign var="invoice" value=true}
            {include file=$row_address}
        </div>
    </div>
    <div class="row text-center">
        <div class="col-md-4">
            <button type="button" class="btn btn-default fixed-width-l">
                <i class="icon icon-times color-red"></i>&nbsp;{l s="Remove DNI" mod='mpgoogleaddress'}
            </button>
        </div>
        <div class="col-md-4">
            <button type="button" class="btn btn-default fixed-width-l">
                <i class="icon icon-times color-red"></i>&nbsp;{l s="Remove VAT" mod='mpgoogleaddress'}
            </button>
        </div>
        <div class="col-md-4">
            <button type="button" class="btn btn-default fixed-width-l" onclick="javascript:printAddress();">
                <i class="icon icon-print color-blue"></i>&nbsp;{l s="Print Address" mod='mpgoogleaddress'}
            </button>
        </div>
    </div>
    <br>
    <div class="row">
        <div id="map-delivery-canvas" style="display: none;"></div>
        <div id="map-invoice-canvas" style="display: none;"></div>
        <div class="col-md-12 text-center">
            <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={$api_key}"></script>
            <div id="map-mpgoogle" style="width: 100%; height: 300px; margin: 10px auto;"></div>
        </div>
        <br>
    </div>
</div>
<script type="text/javascript">
    var daddr = {
        address1: "{$address_delivery->address1}",
        postcode: "{$address_delivery->postcode}",
        city: "{$address_delivery->city}",
        state: "{$address_delivery->state->name}",
        country: "{$address_delivery->country}"
    };
    var iaddr = {
        address1: "{$address_invoice->address1}",
        postcode: "{$address_invoice->postcode}",
        city: "{$address_invoice->city}",
        state: "{$address_invoice->state->name}",
        country: "{$address_invoice->country}"
    };

    $(document).ready(function(){
        var row = $('#tabAddresses').closest('.row');
        var pane = $('#ps_addresses');
        var panel = $(row).closest('.panel');
        $(row).remove();
        $(panel).append($(pane));
        refreshGeocode('delivery');
    });
    function changeAddress()
    {
        var data_type = $('#ps_addresses ul li.active').attr('data-type');
        var id_address = $('#change_delivery_address').val();
        console.log({
            'type': data_type,
            'id_address': id_address,
            'id_order': '{$id_order}'
        });
        $.ajax({
            type: 'post',
            dataType: 'json',
            data:
            {
                ajax: true,
                action: 'changeAddress',
                id_address: id_address,
                id_order: '{$id_order}',
                type: data_type
            },
            success: function(response)
            {
                $.growl.notice({
                    title: '{l s='Operation done.' mod='mpgoogleaddress'}',
                    message: '{l s='Address changed' mod='mpgoogleaddress'}',
                });
                setTimeout(location.reload(),3000);
            },
            error: function(response){
                console.log(response);
            }
        });
    }
    function printAddress()
    {
        var data_type = $('#ps_addresses ul li.active').attr('data-type');
        var id_address = $('#ps_addresses ul li.active').attr('id_address');
        $.ajax({
            url: '{$ajax_print_label}',
            type: 'post',
            data:
            {
                ajax: true,
                action: 'printLabel',
                token: '{$token}',
                id_address: id_address,
                id_order: '{$id_order}'
            },
            success: function(response)
            {
                window.open("{$ajax_print_label}", "Label.pdf");
            },
            error: function(response){
                console.log(response);
            }
        });

    }
    function activatePane()
    {
        event.preventDefault();
        var elem = document.activeElement;
        var type = $(elem).attr('data-type');
        var li = $(elem).closest('li');
        var nav = $(elem).closest('.nav');
        var tabs = $(nav).closest('.row').find('.tab-content');
        var div = $(elem).attr('href');
        $(nav).find('li').removeClass('active');
        $(li).addClass('active');
        $(tabs).find('div').removeClass('active');
        $(div).addClass('active');
        refreshGeocode(type);
    }
    function refreshGeocode(type)
    {
        var addr = {};
        if (type=='delivery') {
            addr = daddr;
        } else {
            addr = iaddr;
        }
        var geocoder = new google.maps.Geocoder();
        var address_data = addr.address1
                +','+
                addr.postcode
                +','+
                addr.city
                +','+
                addr.state
                +','+
                addr.country;
        var address_google = String(address_data).replace(/ /g, '+');

        geocoder.geocode({
            address: address_data
                
            }, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK)
            {
                delivery_map = new google.maps.Map(document.getElementById('map-mpgoogle'), {
                    zoom: 18,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    center: results[0].geometry.location
                });
                var delivery_marker = new google.maps.Marker({
                    map: delivery_map,
                    position: results[0].geometry.location,
                    url: 'http://maps.google.com?q='+address_google
                });
                google.maps.event.addListener(delivery_marker, 'click', function() {
                    window.open(delivery_marker.url);
                });
            }
        });
    }
    function verifyVAT()
    {
        var link = "https://telematici.agenziaentrate.gov.it/VerificaPIVA/Scegli.do?parameter=verificaPiva";
        var text = String($('#vat_number_link').html()).trim();
        copyTextToClipboard(text);
        window.open(link, '_blank');
    }
    function copyTextToClipboard(text) {
        var textArea = document.createElement("textarea");

        textArea.style.position = 'fixed';
        textArea.style.top = 0;
        textArea.style.left = 0;
        textArea.style.width = '2em';
        textArea.style.height = '2em';
        textArea.style.padding = 0;
        textArea.style.border = 'none';
        textArea.style.outline = 'none';
        textArea.style.boxShadow = 'none';
        textArea.style.background = 'transparent';
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            var successful = document.execCommand('copy');
            var msg = successful ? 'successful' : 'unsuccessful';
            console.log('Copying text command was ' + msg);
        } catch (err) {
            console.log('Oops, unable to copy');
        }
        document.body.removeChild(textArea);
    }
</script>   
