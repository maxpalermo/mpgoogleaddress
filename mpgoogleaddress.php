<?php

/* @category  Prestashop Modules
 * @package   mpgoogleaddress
 * @author    mpsoft, Massimiliano Palermo
 * @copyright Copyright 2017 © MPsoft All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')){exit;}
 
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

      parent::__construct();

      $this->displayName = $this->l('Google address viewer with label print');
      $this->description = $this->l('With this module, you are able to to improve the customer address visualization and to print shipping label');

      $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }
  
    public function install()
    {
        if (Shop::isFeatureActive())
        {
          Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() || !$this->registerHook('displayAdminOrder') || !$this->registerHook('displayBackOfficeHeader')) 
        {
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
    
    public function hookDisplayBackOfficeHeader($params)
    {
        //$this->context->controller->addCSS($this->_path.'css/css.css', 'all');
        $backOfficeJS = $this->_path . 'views/js/backOfficeHeader.js';
        $this->context->controller->addJS($backOfficeJS);
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
        $this->context->smarty->assign('address_delivery',$address_delivery);
        $this->context->smarty->assign('address_invoice',$address_invoice);
        $this->context->smarty->assign('state_delivery',$state_delivery);
        $this->context->smarty->assign('state_invoice',$state_invoice);
        $this->context->smarty->assign('api_key', Configuration::get('MPGOOGLEADDRESS_KEY'));
        $this->context->smarty->assign('showmap', Configuration::get('MPGOOGLEADDRESS_SHOW'));
        $this->context->smarty->assign('printlabel', Configuration::get('MPGOOGLEADDRESS_PRINT'));
        $this->context->smarty->assign('http',$this->getHTTP());
        $this->context->smarty->assign('host',$_SERVER['HTTP_HOST']);
        $this->context->smarty->assign('base',$_SERVER['REWRITEBASE']);
        return $this->display(__FILE__, $file);
    }
     
    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit_form'))
        {
            /**
             * Save Logo
             */
            if (!empty($_FILES) && $_FILES['MP_PRINTLABELS_FILE']['error']==0) {
                $fileupd = $_FILES['MP_PRINTLABELS_FILE'];
                $file_name = $fileupd["name"];
                $file_type = $fileupd["type"];
                $file_tmp_name = $fileupd["tmp_name"];
                $file_error = $fileupd["error"];
                $file_size = $fileupd["size"];
                 
                $image = dirname(__FILE__) . "/views/img/image_logo.dat";
                //$image_type = "";
                //if (strpos($file_type,"png")){$image = dirname(__FILE__) . "/image_logo.png";$image_type="png";}
                //if (strpos($file_type,"jpeg")){$image = dirname(__FILE__) . "/image_logo.jpg";$image_type="jpg";}
                //if (strpos($file_type,"jpg")){$image = dirname(__FILE__) . "/image_logo.jpg";$image_type="jpg";}
                //if (strpos($file_type,"gif")){$image = dirname(__FILE__) . "/image_logo.gif";$image_type="gif";}
                
                if (!empty($image))
                {
                    if (move_uploaded_file($file_tmp_name, $image)) 
                    {
                        //set permissions
                        chmod($image,0775);
                        Configuration::updateValue('MP_PRINTLABELS_FILE',basename($image));
                        $this->generateThumb();
                    }
                    else
                    {
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
            $api_key = strval(Tools::getValue('MPGOOGLEADDRESS_KEY'));
            Configuration::updateValue('MPGOOGLEADDRESS_KEY', $api_key);
            $show = (bool)(Tools::getValue('MPGOOGLEADDRESS_SHOW'));
            Configuration::updateValue('MPGOOGLEADDRESS_SHOW', $show);
            $print = (bool)(Tools::getValue('MPGOOGLEADDRESS_PRINT'));
            Configuration::updateValue('MPGOOGLEADDRESS_PRINT', $print);
            $output .= $this->displayConfirmation($this->l('Settings updated'));
            /**
             * Get Print Labels values
             */
            $labelWidth     = strval(Tools::getValue('MP_PRINTLABELS_WIDTH'));
            $labelHeight    = strval(Tools::getValue('MP_PRINTLABELS_HEIGHT'));
            $labelLogo      = strval(Tools::getValue('MP_PRINTLABELS_LOGO'));
            $labelExt       = strval(Tools::getValue('MP_PRINTLABELS_EXT'));
            $labelPhone     = strval(Tools::getValue('MP_PRINTLABELS_PHONE'));
            $labelMobile    = strval(Tools::getValue('MP_PRINTLABELS_MOBILE'));
            $labelOrder     = strval(Tools::getValue('MP_PRINTLABELS_ORDER'));

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
        $this->img_size = file_exists($image) ? number_format(filesize($image)/1024,2) : 0;
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