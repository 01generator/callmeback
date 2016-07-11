<?php
/**
 * 2015 Sarafoudis Nikolaos for 01generator
 *
 * This is a Payment module for Prestashop. This module requires
 *
 *  @author    Sarafoudis Nikolaos for 01generator
 *  @license   MIT License
 */

class CallMeBack extends module
{
    /* Constructor */
    public function __construct()
    {

        if (!defined('_PS_VERSION_')) {
            exit;
        }

        $this->name = 'callmeback';
        $this->tab = 'front_office_features';
        $this->version = '1.0.6';
        $this->author = '01generator';
        $this->module_key = '';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Call me back');
        $this->description = $this->l('Now allow your customers to leave their telephone number so you can call them back');
    }

    /* Installation of module */
    public function install()
    {
        // Install hooks
        // displayProductButtons, displayFooterProduct, displayProductTab, displayProductTabContent
        if (
            !parent::install() ||
            !$this->registerHook('displayProductButtons') ||
            // !$this->registerHook('displayFooterProduct') ||
            !$this->registerHook('actionAdminControllerSetMedia') ||
            !$this->registerHook('displayFooter')
        ) {
            return false;
        }

        if (!$this->installTab('AdminParentCustomer', 'AdminCallMeBack', 'Call me back')) {
            return false;
        }

        // Create SQL table for trasaction tracking
        include_once _PS_MODULE_DIR_ . $this->name . '/callmeback_sql.php';
        $callmebacksql_install = new CallMeBackSQL();
        $callmebacksql_install->createTables();

        // Delete configuration values
        // Configuration::deleteByName('');

        return true;
    }

    /* Unistallation tasks */
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        // Unistall the admin tab
        if (!$this->uninstallTab('AdminCallMeBack')) {
            return false;
        }

        //All went well :)
        return true;
    }

    /* Install the required tabs by the module */
    public function installTab($parent, $class_name, $tab_name)
    {
        // Create new admin tab
        $tab = new Tab();
        $tab->id_parent = (int) Tab::getIdFromClassName($parent);
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }

        $tab->class_name = $class_name;
        $tab->module = $this->name;
        $tab->active = 1;
        return $tab->add();
    }

    /* Unistall the required admin tabs of the module */
    public function uninstallTab($class_name)
    {
        // Retrieve Tab ID
        $id_tab = (int) Tab::getIdFromClassName($class_name);
        // Load tab
        $tab = new Tab((int) $id_tab);
        // Delete it
        return $tab->delete();
    }

    /* Hook Controller */
    public function getHookController($hook_name)
    {
        require_once dirname(__FILE__) . '/controllers/hook/' . $hook_name . '.php';
        $controller_name = $this->name . $hook_name . 'Controller';
        $controller = new $controller_name($this, __FILE__, $this->_path);
        return $controller;
    }

    // public function hookdisplayFooterProduct()
    // {
    //     $config = $this->getConfiguration();
    //     $this->context->smarty->assign('callmeback_config', $config);
    //     return $this->display(__FILE__, 'views/templates/front/callmebutton.tpl');
    // }

    public function hookdisplayProductButtons()
    {
        $config = $this->getConfiguration();
        $config['callmebackimg'] = $this->getBaseUrl() . 'modules/callmeback/phone-call.png';
        $this->context->smarty->assign('callmeback_config', $config);
        return $this->display(__FILE__, 'views/templates/front/callmebutton.tpl');
    }

    public function hookDisplayFooter($params)
    {
        $this->context->controller->addJs($this->_path . 'views/js/callmeback.js');
    }

    // public function getLanguages()
    // {
    //     // Retrieve all enabled languages
    //     $queryLang = 'SELECT id_lang, name, iso_code FROM `' . _DB_PREFIX_ . 'lang` WHERE `active` = 1';
    //     $languages = Db::getInstance()->executeS($queryLang);
    //     return $languages;
    // }

    public function getContent()
    {
        $this->processConfiguration();
        return $this->configurationForm();
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        $this->context->controller->addJS($this->_path . '/views/js/callmeback-admin-notify.js');
    }

    public function configurationForm()
    {
        $languages = Language::getLanguages(false);
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->languages = $languages;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = true;
        $helper->submit_action = 'submitConfigCallMeBack';
        $helper->show_toolbar = true;

        $this->fields_form[0]['form'] = array(
            'tinymce' => false,
            'legend' => array(
                'title' => 'CallMeBack Configuration page.',
            ),
            'submit' => array(
                'name' => 'submitConfigCallMeBack',
                'title' => $this->l('Save '),
                'class' => 'btn btn-default pull-right',
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable surname'),
                    'desc' => $this->l(
                        'If yes when user clicks on callmeback button, ' .
                        'entering his/her surname will be required'
                    ),
                    'name' => 'callmeback_surname',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'callmeback_surname_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'callmeback_surname_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable email'),
                    'desc' => $this->l(
                        'If yes when user clicks on callmeback button, ' .
                        'entering his/her email will be required'
                    ),
                    'name' => 'callmeback_email',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'callmeback_email_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'callmeback_email_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable telephone 2'),
                    'desc' => $this->l(
                        'If yes when user clicks on callmeback button, ' .
                        'entering his/her second telephone will be mandatory ' .
                        'and telephone #1 must be enabled'
                    ),
                    'name' => 'callmeback_telephone2',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'callmeback_telephone2_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'callmeback_telephone2_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable message'),
                    'desc' => $this->l(
                        'If yes when user clicks on callmeback button, ' .
                        'he/she will be able to leave a message for you.'
                    ),
                    'name' => 'callmeback_msg',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'callmeback_msg_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'callmeback_msg_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable hours'),
                    'desc' => $this->l(
                        'If yes when user clicks on callmeback button, ' .
                        'he/she will be able to enter the hours that he/she is available to be called.'
                    ),
                    'name' => 'callmeback_hours',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'callmeback_hours_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'callmeback_hours_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
            ),
        );

        // 5, callmeback_surname, callmeback_email, callmeback_telephone2, callmeback_email, callmeback_msg, callmeback_hours
        $helper->fields_value['callmeback_surname'] = Configuration::get('CALLMEBACK_SURNAME');
        $helper->fields_value['callmeback_email'] = Configuration::get('CALLMEBACK_EMAIL');
        $helper->fields_value['callmeback_telephone2'] = Configuration::get('CALLMEBACK_TELEPHONE2');
        $helper->fields_value['callmeback_msg'] = Configuration::get('CALLMEBACK_MSG');
        $helper->fields_value['callmeback_hours'] = Configuration::get('CALLMEBACK_HOURS');

        return $helper->generateForm($this->fields_form);
    }

    public function processConfiguration()
    {
        if (Tools::isSubmit('submitConfigCallMeBack')) {
            $callmeback_surname = Tools::getValue('callmeback_surname');
            $callmeback_email = Tools::getValue('callmeback_email');
            $callmeback_telephone2 = Tools::getValue('callmeback_telephone2');
            $callmeback_msg = Tools::getValue('callmeback_msg');
            $callmeback_hours = Tools::getValue('callmeback_hours');

            Configuration::updateValue('CALLMEBACK_SURNAME', $callmeback_surname);
            Configuration::updateValue('CALLMEBACK_EMAIL', $callmeback_email);
            Configuration::updateValue('CALLMEBACK_TELEPHONE2', $callmeback_telephone2);
            Configuration::updateValue('CALLMEBACK_MSG', $callmeback_msg);
            Configuration::updateValue('CALLMEBACK_HOURS', $callmeback_hours);

            // all ok :)
            $this->context->smarty->assign('confirmation', 'ok');
        }
    }

    public function getConfiguration()
    {
        $configs = array();
        $configs['callmeback_surname'] = Configuration::get('CALLMEBACK_SURNAME');
        $configs['callmeback_email'] = Configuration::get('CALLMEBACK_EMAIL');
        $configs['callmeback_telephone2'] = Configuration::get('CALLMEBACK_TELEPHONE2');
        $configs['callmeback_msg'] = Configuration::get('CALLMEBACK_MSG');
        $configs['callmeback_hours'] = Configuration::get('CALLMEBACK_HOURS');

        return $configs;
    }

    /* Function to get website url*/
    public function getBaseUrl()
    {
        if (_PS_BASE_URL_SSL_) {
            $base_url = _PS_BASE_URL_SSL_;
        } else {
            $base_url = _PS_BASE_URL_;
        }

        if (__PS_BASE_URI__) {
            $base_url .= __PS_BASE_URI__;
        }

        return $base_url;
    }

    public function getTranslatedAjaxMessages()
    {
        $trans_ajax_msg = array(
            'form_error' => $this->l('Form submission resulted in an error. Please contact the administrator.'),
            'form_error_name' => $this->l('Name field is required.'),
            'form_error_surname' => $this->l('Surname field is required.'),
            'form_error_email' => $this->l('Email field is required.'),
            'form_error_telephone_field' => $this->l('Telephone field is required.'),
            'form_error_telephone' => $this->l('Telephone is not correct, it should be 10 digits.'),
        );
        return $trans_ajax_msg;
    }
}
