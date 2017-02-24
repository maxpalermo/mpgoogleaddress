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
 
class MpGoogleAddress extends Module
{
    private $img_url;
    private $img_size;
    
    public function __construct()
    {
        $this->name = 'mpgoogleaddress';
        $this->tab = 'administration';
        $this->version = '1.2.1';
        $this->author = 'mpsoft';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->module_key = '9d375a6411ac69cd994ead6338cc5f6d';

        parent::__construct();
        
        $this->displayName = $this->l('Google address viewer with label print');
        $this->description =
            $this->l('Enhance customer address visualization and print shipping label');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }
  
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install()
                || !$this->registerHook('displayAdminOrder')) {
            return false;
        }
        return true;
    }
    
    public function uninstall()
    {
        if (!parent::uninstall()
            && ConfigurationCore::deleteByName('MPGOOGLEADDRESS_PRINT')
            && ConfigurationCore::deleteByName('MPGOOGLEADDRESS_SHOW')
            && ConfigurationCore::deleteByName('MPGOOGLEADDRESS_KEY')) {
            return false;
        }
        return true;
    }
    
    public function hookDisplayAdminOrder($params)
    {
        $file = '/views/templates/hook/googlemap.tpl';
        $id_order = (int)Tools::getValue('id_order');
        $order = new OrderCore($id_order);
        $address_delivery = new AddressCore($order->id_address_delivery);
        $address_invoice = new AddressCore($order->id_address_invoice);
        $state_delivery = new StateCore($address_delivery->id_state);
        $state_invoice = new StateCore($address_invoice->id_state);
        $this->context->smarty->assign('address_delivery', $address_delivery);
        $this->context->smarty->assign('address_invoice', $address_invoice);
        $this->context->smarty->assign('state_delivery', $state_delivery);
        $this->context->smarty->assign('state_invoice', $state_invoice);
        $this->context->smarty->assign('api_key', Configuration::get('MPGOOGLEADDRESS_KEY'));
        $this->context->smarty->assign('showmap', Configuration::get('MPGOOGLEADDRESS_SHOW'));
        $this->context->smarty->assign('printlabel', Configuration::get('MPGOOGLEADDRESS_PRINT'));
        $this->context->smarty->assign('ajax_folder', $this->getViewsPath() . 'ajax/');
        $this->smarty->assign('address_id_order', Context::getContext()->controller->tpl_view_vars['order']->id);
        return $this->display(__FILE__, $file);
    }
     
    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit_form')) {
            /**
             * Save Logo
             */
            if (!empty($_FILES) && $_FILES['MP_PRINTLABELS_FILE']['error']==0) {
                $fileupd = $_FILES['MP_PRINTLABELS_FILE'];
                //$file_name = $fileupd["name"];
                //$file_type = $fileupd["type"];
                $file_tmp_name = $fileupd["tmp_name"];
                //$file_error = $fileupd["error"];
                //$file_size = $fileupd["size"];
                 
                $image = dirname(__FILE__) . "/views/img/image_logo.dat";
                //$image_type = "";
                //if (strpos($file_type,"png")){$image = dirname(__FILE__) . "/image_logo.png";$image_type="png";}
                //if (strpos($file_type,"jpeg")){$image = dirname(__FILE__) . "/image_logo.jpg";$image_type="jpg";}
                //if (strpos($file_type,"jpg")){$image = dirname(__FILE__) . "/image_logo.jpg";$image_type="jpg";}
                //if (strpos($file_type,"gif")){$image = dirname(__FILE__) . "/image_logo.gif";$image_type="gif";}
                
                if (!empty($image)) {
                    if (move_uploaded_file($file_tmp_name, $image)) {
                        //set permissions
                        chmod($image, 0775);
                        Configuration::updateValue('MP_PRINTLABELS_FILE', basename($image));
                        $this->generateThumb();
                    } else {
                        $this->image_url = "";
                        $this->img_size  = 0;
                    }
                    
                } else {
                    $this->generateThumb();
                }
            } else {
                $this->generateThumb();
            }
            /**
             * Get Google map values
             */
            $api_key = (string)(Tools::getValue('MPGOOGLEADDRESS_KEY'));
            Configuration::updateValue('MPGOOGLEADDRESS_KEY', $api_key);
            $show = (bool)(Tools::getValue('MPGOOGLEADDRESS_SHOW'));
            Configuration::updateValue('MPGOOGLEADDRESS_SHOW', $show);
            $print = (bool)(Tools::getValue('MPGOOGLEADDRESS_PRINT'));
            Configuration::updateValue('MPGOOGLEADDRESS_PRINT', $print);
            $output .= $this->displayConfirmation($this->l('Settings updated'));
            /**
             * Get Print Labels values
             */
            $labelWidth     = (string)(Tools::getValue('MP_PRINTLABELS_WIDTH'));
            $labelHeight    = (string)(Tools::getValue('MP_PRINTLABELS_HEIGHT'));
            $labelLogo      = (string)(Tools::getValue('MP_PRINTLABELS_LOGO'));
            $labelExt       = (string)(Tools::getValue('MP_PRINTLABELS_EXT'));
            $labelPhone     = (string)(Tools::getValue('MP_PRINTLABELS_PHONE'));
            $labelMobile    = (string)(Tools::getValue('MP_PRINTLABELS_MOBILE'));
            $labelOrder     = (string)(Tools::getValue('MP_PRINTLABELS_ORDER'));

            Configuration::updateValue('MP_PRINTLABELS_WIDTH', $labelWidth);
            Configuration::updateValue('MP_PRINTLABELS_HEIGHT', $labelHeight);
            Configuration::updateValue('MP_PRINTLABELS_LOGO', $labelLogo);
            Configuration::updateValue('MP_PRINTLABELS_EXT', $labelExt);
            Configuration::updateValue('MP_PRINTLABELS_PHONE', $labelPhone);
            Configuration::updateValue('MP_PRINTLABELS_MOBILE', $labelMobile);
            Configuration::updateValue('MP_PRINTLABELS_ORDER', $labelOrder);
        }
        return $output.$this->displayForm();
    }
    
    public function generateThumb()
    {
        $image = dirname(__FILE__) . "/" . Configuration::get('MP_PRINTLABELS_FILE');
        $this->img_url  = ImageManager::thumbnail($image, 'printlabel_logo.jpg', 200, 'jpg', true, true);
        $this->img_size = file_exists($image) ? number_format(filesize($image)/1024, 2) : 0;
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
                        'required'  => true,
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

        return $helper->generateForm($fields_form) . $this->addJS();
    }
    
    private function addJS()
    {
        return '<script type="text/javascript">'. PHP_EOL .
                '$(document).ready(function() ' . PHP_EOL .
                    '{' . PHP_EOL .
                        '//accept only image'. PHP_EOL .
                        '$("#MP_PRINTLABELS_FILE").attr("accept","image/*");'. PHP_EOL .
                    '});'. PHP_EOL .
                '</script>';
    }
    
    /**
     * Get the root payh of site
     */
    private function getRootPath()
    {
        $base = $this->addEndingSlash(filter_input(INPUT_SERVER, 'REWRITEBASE'));
        if (Tools::getProtocol()=='http') {
            $url = _PS_BASE_URL_ . $base;
        } else {
            $url = _PS_BASE_URL_SSL_ . $base;
        }
        return $this->addEndingSlash($url);
    }
    
    /**
     * 
     * @return string full url /modules
     */
    private function getModulePath()
    {
        $module = $this->getRootPath() . 'modules/';
        return $module;
    }
    
    /**
     * 
     * @return string full url /views
     */
    private function getViewsPath()
    {
        $module = $this->addEndingSlash($this->name);
        return $this->addEndingSlash($this->getModulePath()) . $module . "views/";
    }
    
    private function addEndingSlash($path)
    {
        $slash_type = (strpos($path, '\\')===0) ? 'win' : 'unix';
        $last_char = Tools::substr($path, Tools::strlen($path)-1, 1);
        if ($last_char != '/' and $last_char != '\\') {
            // no slash:
            $path .= ($slash_type == 'win') ? '\\' : '/';
        }
        return $path;
    }
}
