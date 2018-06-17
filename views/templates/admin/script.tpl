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

<script type="text/javascript">
	$(document).ready(function(){
		var row = $('#tabAddresses').closest('.row');
		var pane = $('#ps_addresses');
		var panel = $(row).closest('.panel');
		$(row).remove();
		$(panel).append($(pane));
	});
    function activatePane()
    {
        event.preventDefault();
        var elem = document.activeElement;
        var li = $(elem).closest('li');
        var nav = $(elem).closest('.nav');
        var tabs = $(nav).closest('.row').find('.tab-content');
        var div = $(elem).attr('href');
        $(nav).find('li').removeClass('active');
        $(li).addClass('active');
        $(tabs).find('div').removeClass('active');
        $(div).addClass('active');
    }
</script>
