<?php
/**
 * This module shows enhanced custom address fields and google map visualization for both address tabs
 * 
 * @author    mpsoft, Massimiliano Palermo
 * 
 * @category  Prestashop Modules
 * 
 * @package   mpgoogleaddress
 * 
 * @copyright Copyright 2017 © MPsoft All right reserved
 * 
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
 
class MpGoogleAddress extends Module
{
    public function __construct()
    {
        $this->name = 'mpgoogleaddress';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'mpsoft';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Google address viewer');
        $this->description = $this->l('With this module, you are able to to fix bug in google maps visualization in order tab.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }
  
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() || !$this->registerHook('displayAdminOrder')) {
            return false;
        }
        return true;
    }
    
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
            return true;
    }
    
    public function hookdisplayAdminOrder($params)
    {
        //$this->context->controller->addJS(($this->_path).'js/clipboard.js');
        $id_order = (int)Tools::getValue('id_order');
        $order = new OrderCore($id_order);
        $address_delivery = new AddressCore($order->id_address_delivery);
        $address_invoice = new AddressCore($order->id_address_invoice);
        $state_delivery = new StateCore($address_delivery->id_state);
        $state_invoice = new StateCore($address_invoice->id_state);
        $http = '';
        if (!empty($_SERVER['HTTPS']) && Tools::strtoupper($_SERVER['HTTPS'])=='ON') {
            $http = 'https://';
        } else {
            $http = 'http://';
        }
        $module = $http . $_SERVER['HTTP_HOST'] .  $_SERVER['REWRITEBASE'] . "modules/mpgoogleaddress/";
        $this->context->smarty->assign('module_path', $module);
        $this->context->smarty->assign('img', $module . 'views/img/');
        $this->context->smarty->assign('address_delivery', $address_delivery);
        $this->context->smarty->assign('address_invoice', $address_invoice);
        $this->context->smarty->assign('state_delivery', $state_delivery);
        $this->context->smarty->assign('state_invoice', $state_invoice);
        $this->context->smarty->assign('api_key', Configuration::get('MPGOOGLEADDRESS_KEY'));
        return $this->display(__FILE__, 'googlemap.tpl');
    }
     
    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit_form')) {
            $api_key = (string)(Tools::getValue('MPGOOGLEADDRESS_KEY'));
            Configuration::updateValue('MPGOOGLEADDRESS_KEY', $api_key);
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        return $output.$this->displayForm();
    }
    
    public function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form = [];
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('API Key'),
                    'name' => 'MPGOOGLEADDRESS_KEY',
                    'size' => 20,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submit_form'
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit_form';
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['MPGOOGLEADDRESS_KEY'] = Configuration::get('MPGOOGLEADDRESS_KEY');

        return $helper->generateForm($fields_form);
    }
}
