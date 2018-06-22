<?php
/**
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
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_TOOL_DIR_.'tcpdf/config/lang/eng.php');
require_once(_PS_TOOL_DIR_.'tcpdf/tcpdf.php');

class MpGoogleAddress extends Module
{
    private $img_url;
    private $img_size;
    
    public function __construct()
    {
        $this->name = 'mpgoogleaddress';
        $this->tab = 'administration';
        $this->version = '1.4.4';
        $this->author = 'Digital Solutions®';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->module_key = '9d375a6411ac69cd994ead6338cc5f6d';

        parent::__construct();
        
        $this->displayName = $this->l('Google address viewer with label print');
        $this->description =
            $this->l('Enhance customer address visualization and print shipping label');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->context = ContextCore::getContext();
    }
    
    /**
     * Return the Admin Template Path
     * @return string The admin template path
     */
    public function getAdminTemplatePath()
    {
        return $this->getPath().'views/templates/admin/';
    }

    /**
     * Get The URL path of this module
     * @return string The URL of this module
     */
    public function getUrl()
    {
        return $this->_path;
    }
    
    /**
     * Return the physical path of this module
     * @return string The path of this module
     */
    public function getPath()
    {
        return $this->local_path;
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install()
                || !$this->registerHook('displayAdminOrder')
                || !$this->registerHook('displayBackOfficeHeader')) {
            return false;
        }
        return true;
    }
    
    public function uninstall()
    {
        $db=Db::getInstance();
        $db->delete('configuration', 'name like \'MP_PRINTLABEL%\'');
        $db->delete('configuration', 'name like \'MPGOOGLEADDRESS%\'');
        
        return parent::uninstall();
    }
    
    public function hookDisplayBackOfficeHeader($params)
    {
        if (empty($params)) {
            //nothing
        }
        
        $this->context->controller->addCSS(
            'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'
        );
    }
    
    public function hookDisplayAdminOrder($params)
    {
        if (empty($params)) {
            //dummy
        }
        
        $link = new LinkCore();
        $ajax_path = str_replace('/module/', '/modules/', $link->getModuleLink($this->name, 'ajax.php'));
        $id_order = (int)Tools::getValue('id_order');
        $order = new Order($id_order);
        $address_delivery = $this->getAddress($order->id_address_delivery);
        $address_invoice = $this->getAddress($order->id_address_invoice);
        $addresses = $this->getAddresses($order->id_customer);
        $tpl_vars = array(
            'id_order' => Tools::getValue('id_order'),
            'address_delivery' => $address_delivery,
            'address_invoice' => $address_invoice,
            'addresses' => $addresses,
            'api_key' => Configuration::get('MPGOOGLEADDRESS_KEY'),
            'showmap' => Configuration::get('MPGOOGLEADDRESS_SHOW'),
            'printlabel', Configuration::get('MPGOOGLEADDRESS_PRINT'),
            'verify_vat', Configuration::get('MPGOOGLEADDRESS_VERIFY_VAT'),
            'http' => $this->getHTTP(),
            'ajax_url' => $ajax_path,
            'ajax_print_label' => $this->getUrl().'ajax_print_label.php',
            'ajax_change_address' => $this->getUrl().'ajax_change_address.php',
            'ajax_show_address' => $this->getUrl().'ajax_show_address.php',
            'token' => Tools::encrypt($this->name),
            'tab_address' => $this->getAdminTemplatePath().'tab_address.tpl',
            'tab_invoice' => $this->getAdminTemplatePath().'tab_invoice.tpl',
            'row_address' => $this->getAdminTemplatePath().'row_address.tpl',
            'jquery_map' => $this->getUrl().'views/js/jquery.googlemap.js',
            'script' => $this->getAdminTemplatePath().'script.tpl',
            'address_id_order' => Context::getContext()->controller->tpl_view_vars['order']->id,
        );
        $this->context->smarty->assign($tpl_vars);
        //PrestaShopLoggerCore::addLog(print_r($tpl_vars,1));
        $addresses_tpl = $this->getAdminTemplatePath().'addresses.tpl';
        //$this->smarty->clearCache($addresses_tpl);
        return $this->context->smarty->fetch($addresses_tpl);
    }
    
    public function getAddresses($id_customer)
    {
        $db = Db::getInstance();
        $sql = "select id_address from "._DB_PREFIX_."address "
            ."where id_customer=".(int)$id_customer
            ." and active=1";
        $result = $db->executeS($sql);
        $output = array();
        foreach ($result as $addr) {
            $output[] = $this->getAddress($addr['id_address']);
        }
        return $output;
    }

    public function getAddress($id_address)
    {
        $address = new Address($id_address);
        $state = new StateCore($address->id_state);
        $address->state = $state;
        return $address;
    }

    public function displayAjaxPrintAddress()
    {
        $id_address = (int)Tools::getValue('id_address');
        $id_order = (int)Tools::getValue('id_order');
        ob_start();
        ob_end_clean();
        header("Content-type:application/pdf");
        print $this->pdf($id_order, $id_address);
        exit();
    }

    public function displayAjaxCreateLabel()
    {
        $id_order = (int)Tools::getValue('id_order', 0);
        $address_type = Tools::getValue('address_type', 'shipping');
        $file =  $this->pdf($id_order, $address_type);
        print $this->getUrl().'pdf/'.basename($file);
        exit();
    }
    
    public function ajaxProcessRemoveDataFromAddress()
    {
        $id_order = (int)Tools::getValue('id_order', 0);
        $fieldname = Tools::getValue('fieldname', '');
        $addressType = Tools::getValue('address', '');
        if (!$id_order) {
            $this->printAjaxError($this->l('Invalid Order Id'));
            exit();
        }
        if (!$fieldname) {
            $this->printAjaxError($this->l('Invalid Field name'));
            exit();
        }
        if (!$addressType || !($addressType=='invoice' || $addressType=='delivery')) {
            $this->printAjaxError($this->l('Invalid Address type'));
            exit();
        }
        $order = new OrderCore($id_order);
        $address = array();
        if ($addressType == 'delivery') {
            $address = new Address($order->id_address_delivery);
        } else {
            $address = new Address($order->id_address_invoice);
        }
        $db = Db::getInstance();
        $result = $db->update(
            'address',
            array(
                $fieldname => ''
            ),
            'id_address='.(int)$address->id
        );
        if ($result) {
            if ($fieldname == 'dni') {
                $this->printAjaxConfirmation($this->l('Dni deleted'));
            } else {
                $this->printAjaxConfirmation($this->l('Vat number deleted'));
            }
        } else {
            $this->printAjaxError($this->l('Error deleting field.'));
        }
        exit();
    }
    
    public function printAjaxConfirmation($message)
    {
        print Tools::jsonEncode(
            array(
                'error' => false,
                'message' => $message,
            )
        );
    }
    
    public function printAjaxError($error)
    {
        print Tools::jsonEncode(
            array(
                'error' => true,
                'message' => $error,
            )
        );
    }
    
    public function getContent()
    {
        $output = '';
        $this->generateThumb();
        
        if (Tools::isSubmit('submit_form')) {
            /**
             * Save Logo
             */
            $file = Tools::fileAttachment('MP_PRINTLABELS_FILE');
            if (!empty($file)) {
                $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
                $mimeType = array('image/png', 'image/x-png', 'image/jpg', 'image/gif', 'image/jpeg');

                if (!in_array($file['mime'], $mimeType)) {
                    $this->context->smarty->assign(
                        'warning',
                        $this->displayWarning(
                            $this->l('Bad format image file')
                            . ': '
                            . $this->l('Only (jpg/gif/png) image file accepted')
                        )
                    );
                } else {
                    $dest = _PS_MODULE_DIR_ . 'mpgoogleaddress/views/img/printlabel_logo' . '.' . $fileExt;
                    $remove = glob(_PS_MODULE_DIR_ . 'mpgoogleaddress/views/img/printlabel_logo.*');
                    foreach ($remove as $logo) {
                        unlink($logo);
                    }
                    move_uploaded_file($file['tmp_name'], $dest);
                    ConfigurationCore::updateValue('MP_PRINTLABELS_FILE', $dest);
                    $this->generateThumb();
                }
            }
            /**
             * Get Google map values
             */
            $api_key = (string)(Tools::getValue('MPGOOGLEADDRESS_KEY'));
            
            if (empty($api_key)) {
                $error = $this->displayError($this->l('Please insert a valid API Key for map visualization'));
                return $error . $this->displayForm();
            }
            
            $show = (bool)(Tools::getValue('MPGOOGLEADDRESS_SHOW'));
            $print = (bool)(Tools::getValue('MPGOOGLEADDRESS_PRINT'));
            $output .= $this->displayConfirmation($this->l('Settings updated'));
            /**
             * Get Print Labels values
             */
            $labelWidth     = (int)(Tools::getValue('MP_PRINTLABELS_WIDTH'));
            $labelHeight    = (int)(Tools::getValue('MP_PRINTLABELS_HEIGHT'));
            
            if ($labelWidth == 0) {
                $error = $this->displayError($this->l('Please insert a valid label width'));
                return $error . $this->displayForm();
            }
            
            if ($labelHeight == 0) {
                $error = $this->displayError($this->l('Please insert a valid label height'));
                return $error . $this->displayForm();
            }
            
            
            $labelLogo      = (string)(Tools::getValue('MP_PRINTLABELS_LOGO'));
            $labelExt       = (string)(Tools::getValue('MP_PRINTLABELS_EXT'));
            $labelPhone     = (string)(Tools::getValue('MP_PRINTLABELS_PHONE'));
            $labelMobile    = (string)(Tools::getValue('MP_PRINTLABELS_MOBILE'));
            $labelOrder     = (string)(Tools::getValue('MP_PRINTLABELS_ORDER'));

            Configuration::updateValue('MPGOOGLEADDRESS_KEY', $api_key);
            Configuration::updateValue('MPGOOGLEADDRESS_SHOW', $show);
            Configuration::updateValue('MPGOOGLEADDRESS_PRINT', $print);
            Configuration::updateValue('MP_PRINTLABELS_WIDTH', $labelWidth);
            Configuration::updateValue('MP_PRINTLABELS_HEIGHT', $labelHeight);
            Configuration::updateValue('MP_PRINTLABELS_LOGO', $labelLogo);
            Configuration::updateValue('MP_PRINTLABELS_EXT', $labelExt);
            Configuration::updateValue('MP_PRINTLABELS_PHONE', $labelPhone);
            Configuration::updateValue('MP_PRINTLABELS_MOBILE', $labelMobile);
            Configuration::updateValue('MP_PRINTLABELS_ORDER', $labelOrder);
            Configuration::updateValue(
                'MPGOOGLEADDRESS_VERIFY_VAT',
                'https://telematici.agenziaentrate.gov.it/VerificaPIVA/Scegli.do?parameter=verificaPiva'
            );
        }
        $js = _PS_MODULE_DIR_ . 'mpgoogleaddress/views/js/imagePreview.js';
        if (file_exists($js)) {
            $this->context->controller->addJS($js);
        } else {
            $output .= $this->displayWarning('Script not found: ' . $js);
        }
        
        return $output.$this->displayForm();
    }
    
    public function generateThumb()
    {
        $filename = Configuration::get('MP_PRINTLABELS_FILE');
        if (!empty($filename) && file_exists($filename)) {
            $this->img_url  = ImageManager::thumbnail($filename, 'printlabel_logo.jpg', 200, 'jpg', true, true);
            $this->img_size = Tools::formatBytes(filesize($filename)/1024);
        } else {
            $this->img_url  = '';
            $this->img_size = 0;
        }
    }
    
    public function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form = array();
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
                ),
                array(
                    'type'    => 'switch',
                    'label'   => $this->l('Show Google Maps'),
                    'name'    => 'MPGOOGLEADDRESS_SHOW',
                    'is_bool' => true,

                    'desc'    => $this->l('If set, replace standard google map with this one'),
                    'values'  => array(
                        array(
                            'id'    => 'active_on',
                            'value' => true,
                            'label' => $this->l('YES')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => false,
                            'label' => $this->l('NO')
                        )
                    ),
                ),
                array(
                    'type'    => 'switch',
                    'label'   => $this->l('Show Print Label Button'),
                    'name'    => 'MPGOOGLEADDRESS_PRINT',
                    'is_bool' => true,

                    'desc'    => $this->l('If set, show a button to print shipping label'),
                    'values'  => array(
                        array(
                            'id'    => 'active_on',
                            'value' => true,
                            'label' => $this->l('YES')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => false,
                            'label' => $this->l('NO')
                        )
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submit_form'
            )
        );
        $fields_form[1]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Print Label Settings'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Label Width (mm)'),
                        'name' => 'MP_PRINTLABELS_WIDTH',
                        'size' => 20,
                        'required' => true,
                        'class' => 'input fixed-width-sm'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Label Height (mm)'),
                        'name' => 'MP_PRINTLABELS_HEIGHT',
                        'size' => 20,
                        'required' => true,
                        'class' => 'input fixed-width-sm'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Logo'),
                        'name' => 'MP_PRINTLABELS_LOGO',
                        'is_bool' => true,
                        'desc' => $this->l('If active, show logo picture on label.'),
                        'values' => array(
                                        array(
                                                'id' => 'logo_active_on',
                                                'value' => 1,
                                                'label' => $this->l('YES')
                                        ),
                                        array(
                                                'id' => 'logo_active_off',
                                                'value' => 0,
                                                'label' => $this->l('NO')
                                        )
                                ),
                        ),
                    array(
                        'type'      => 'file',
                        'label'     => $this->l('Select a file:'),
                        'desc'      => $this->l('Logo Picture. Extension allowed: jpeg, png, jpg, gif.'),
                        'name'      => 'MP_PRINTLABELS_FILE',
                        'lang'      => true,
                        'display_image' => true,
                        'image' => $this->img_url,
                        'size' => $this->img_size,
                        'accept' => '*.png,*.jpg,*.jpeg,*.gif'
                       ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Phone'),
                        'name' => 'MP_PRINTLABELS_PHONE',
                        'is_bool' => true,
                        'desc' => $this->l('If active, show phone number on label.'),
                        'values' => array(
                                        array(
                                                'id' => 'phone_active_on',
                                                'value' => 1,
                                                'label' => $this->l('YES')
                                        ),
                                        array(
                                                'id' => 'phone_active_off',
                                                'value' => 0,
                                                'label' => $this->l('NO')
                                        )
                                ),
                        ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Mobile phone'),
                        'name' => 'MP_PRINTLABELS_MOBILE',
                        'is_bool' => true,
                        'desc' => $this->l('If active, show mobile phone on label.'),
                        'values' => array(
                                        array(
                                                'id' => 'mobile_active_on',
                                                'value' => 1,
                                                'label' => $this->l('YES')
                                        ),
                                        array(
                                                'id' => 'mobile_active_off',
                                                'value' => 0,
                                                'label' => $this->l('NO')
                                        )
                                ),
                        ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Order'),
                        'name' => 'MP_PRINTLABELS_ORDER',
                        'is_bool' => true,
                        'desc' => $this->l('If active, show order reference on label.'),
                        'values' => array(
                                        array(
                                                'id' => 'order_active_on',
                                                'value' => 1,
                                                'label' => $this->l('YES')
                                        ),
                                        array(
                                                'id' => 'order_active_off',
                                                'value' => 0,
                                                'label' => $this->l('NO')
                                        )
                                ),
                        ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name'  => 'submit_form'
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
        $helper->fields_value['MPGOOGLEADDRESS_SHOW'] = Configuration::get('MPGOOGLEADDRESS_SHOW');
        $helper->fields_value['MPGOOGLEADDRESS_PRINT'] = Configuration::get('MPGOOGLEADDRESS_PRINT');
        // Load print labels values
        $helper->fields_value['MP_PRINTLABELS_WIDTH'] = Configuration::get('MP_PRINTLABELS_WIDTH');
        $helper->fields_value['MP_PRINTLABELS_HEIGHT'] = Configuration::get('MP_PRINTLABELS_HEIGHT');
        $helper->fields_value['MP_PRINTLABELS_LOGO'] = Configuration::get('MP_PRINTLABELS_LOGO');
        $helper->fields_value['MP_PRINTLABELS_PHONE'] = Configuration::get('MP_PRINTLABELS_PHONE');
        $helper->fields_value['MP_PRINTLABELS_MOBILE'] = Configuration::get('MP_PRINTLABELS_MOBILE');
        $helper->fields_value['MP_PRINTLABELS_ORDER'] = Configuration::get('MP_PRINTLABELS_ORDER');

        return $helper->generateForm($fields_form);
    }
    
    /**
     * Check if HTTPS is activated
     * @return string
     */
    private function getHTTP()
    {
        if (empty($_SERVER['HTTPS'])) {
            return "http://";
        }
        return 'https://';
    }
}
