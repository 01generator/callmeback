<?php
/**
 * 2015 Sarafoudis Nikolaos for 01generator
 *
 * This is a Payment module for Prestashop. This module requires
 *
 *  @author    Sarafoudis Nikolaos for 01generator
 *  @license   MIT License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class CallMeBack
 */
class CallMeBack extends Module
{
    const SURNAME = 'CALLMEBACK_SURNAME';
    const EMAIL = 'CALLMEBACK_EMAIL';
    const TELEPHONE2 = 'CALLMEBACK_TELEPHONE2';
    const MESSAGE = 'CALLMEBACK_MSG';
    const HOURS = 'CALLMEBACK_HOURS';

    /**
     * CallMeBack constructor.
     */
    public function __construct()
    {

        $this->name = 'callmeback';
        $this->tab = 'front_office_features';
        $this->version = '1.0.7';
        $this->author = '01generator';
        $this->module_key = '';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Call me back');
        $this->description = $this->l('Now allow your customers to leave their telephone number so you can call them back');

        $this->controllers = array('ajax');
    }

    /**
     * Install this module
     *
     * @return bool Whether the module has been successfully installed
     * @throws PrestaShopException
     */
    public function install()
    {
        // Install hooks
        // displayProductButtons, displayFooterProduct, displayProductTab, displayProductTabContent
        if (!parent::install() ||
            !$this->registerHook('displayProductButtons') ||
            !$this->registerHook('actionAdminControllerSetMedia') ||
            !$this->registerHook('displayFooter') ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('displayBackOfficeHeader')
        ) {
            return false;
        }

        if (!$this->installTab('AdminParentCustomer', 'AdminCallMeBack', 'Call me back')) {
            return false;
        }

        // Create SQL table for trasaction tracking
        include_once _PS_MODULE_DIR_.$this->name.'/sql/CallMeBackSql.php';
        $callMeBackSqlInstall = new CallMeBackSql();
        $callMeBackSqlInstall->createTables();

        return true;
    }

    /**
     * Uninstall this module
     *
     * @return bool Whether this module has been successfully uninstalled
     */
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

    /**
     * Install the required tabs by the module
     *
     * @param string $parent    Parent class name
     * @param string $className Class name of new tab
     * @param string $tabName   Tab name of new tab
     * @return int New tab ID
     */
    public function installTab($parent, $className, $tabName)
    {
        // Create new admin tab
        $tab = new Tab();
        $tab->id_parent = (int) Tab::getIdFromClassName($parent);
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }

        $tab->class_name = $className;
        $tab->module = $this->name;
        $tab->active = 1;

        return $tab->add();
    }

    /**
     * Uninstall the required admin tabs of the module
     *
     * @param string $className Class name of tab to be uninstalled
     * @return bool Whether the tab has been successfully uninstalled
     */
    public function uninstallTab($className)
    {
        // Retrieve Tab ID
        $idTab = (int) Tab::getIdFromClassName($className);
        // Load tab
        $tab = new Tab((int) $idTab);
        // Delete it
        return $tab->delete();
    }

    /**
     * Get hook Controller
     *
     * @param string $hookName Name of hook
     * @return mixed
     */
    public function getHookController($hookName)
    {
        require_once dirname(__FILE__).'/controllers/hook/'.$hookName.'.php';
        $controllerName = $this->name.$hookName.'Controller';
        $controller = new $controllerName($this, __FILE__, $this->_path);

        return $controller;
    }

    /**
     * Hook to FO HEAD tags
     *
     * @return string Hook HTML
     */
    public function hookDisplayHeader()
    {
        $ip = $this->getUserIP();
        $current_country = $this->getIPInfo($ip, "Country Code");

        if (Tools::isEmpty($current_country)) {
            $current_country = Context::getContext()->country->iso_code;
        }

        $active_countries = array_column(Country::getCountries(Context::getContext()->country->iso_code), 'iso_code');
        
        // Show preffered country codes in dropdown list, only if there are a few countries available.
        if (sizeof($active_countries) < 5) {
            $this->context->smarty->assign(
                array(
                    'prefered_countries' => $active_countries,
                )
            );
        }
        else {
            $this->context->smarty->assign(
                array(
                    'prefered_countries' => [Context::getContext()->country->iso_code],
                )
            );
        }

        $this->context->smarty->assign(
            array(
                'dir' => $this->getBaseUrl() . 'modules/' . $this->name . '/views/js/',
                'callmeback_ajax' => $this->context->link->getModuleLink($this->name, 'ajax', array(), null, null, true),
                'current_country' => $current_country,
            )
        );

        return $this->display(__FILE__, 'views/templates/front/jsdef.tpl');
    }

    /**
     * Hook to BO HEAD tags
     *
     * @return string Hook HTML
     */
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->smarty->assign(array(
            'callmeback_ajax' => $this->context->link->getModuleLink($this->name, 'ajax', array(), null, null, true),
        ));

        return $this->display(__FILE__, 'views/templates/admin/jsdef.tpl');
    }

    /**
     * Hook to display Product Buttons
     *
     * @return string Hook HTML
     */
    public function hookdisplayProductButtons()
    {   
        $config = $this->getConfiguration();
        $config['callmebackimg'] = $this->getBaseUrl().'modules/callmeback/phone-call.png';
        $this->context->smarty->assign(
            array(
                'callmeback_config' => $config,
            )
        );

        return $this->display(__FILE__, 'views/templates/front/callmebutton.tpl');
    }

    /**
     * Hook to display footer
     *
     * @param array $params Hook parameters
     */
    public function hookDisplayFooter($params)
    {   
        if (isset(Context::getContext()->controller->php_self) && Context::getContext()->controller->php_self == 'product') {
            $this->context->controller->addJs($this->_path.'views/js/callmeback.js');
            $this->context->controller->addJs($this->_path.'views/js/intlTelInput.min.js');
            $this->context->controller->addCss($this->_path.'views/css/intlTelInput.min.css');
        }
        
    }

    /**
     * Get module configuration page
     *
     * @return string Configuration page HTML
     */
    public function getContent()
    {
        $this->processConfiguration();

        return $this->configurationForm();
    }

    /**
     * Hook to set media method of AdminController
     *
     * @param array $params Hook parameters
     */
    public function hookActionAdminControllerSetMedia($params)
    {
        $this->context->controller->addJS($this->_path.'/views/js/callmeback-admin-notify.js');
    }

    /**
     * Generate configuration form
     *
     * @return string Configuration form HTML
     */
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
                        'If yes when user clicks on callmeback button, entering his/her surname will be required'
                    ),
                    'name' => self::SURNAME,
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
                        'If yes when user clicks on callmeback button, entering his/her email will be required'
                    ),
                    'name' => self::EMAIL,
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
                        'If yes when user clicks on callmeback button, entering his/her second telephone will be mandatory and telephone #1 must be enabled'
                    ),
                    'name' => self::TELEPHONE2,
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
                        'If yes when user clicks on callmeback button, he/she will be able to leave a message to you.'
                    ),
                    'name' => self::MESSAGE,
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
                        'If yes when user clicks on callmeback button, he/she will be able to enter the hours that he/she is available to be called.'
                    ),
                    'name' => self::HOURS,
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
        $helper->fields_value[self::SURNAME] = Configuration::get(self::SURNAME);
        $helper->fields_value[self::EMAIL] = Configuration::get(self::EMAIL);
        $helper->fields_value[self::TELEPHONE2] = Configuration::get(self::TELEPHONE2);
        $helper->fields_value[self::MESSAGE] = Configuration::get(self::MESSAGE);
        $helper->fields_value[self::HOURS] = Configuration::get(self::HOURS);

        return $helper->generateForm($this->fields_form);
    }

    /**
     * Process submitted configuration
     */
    public function processConfiguration()
    {
        if (Tools::isSubmit('submitConfigCallMeBack')) {
            Configuration::updateValue(self::SURNAME, Tools::getValue(self::SURNAME));
            Configuration::updateValue(self::EMAIL, Tools::getValue(self::EMAIL));
            Configuration::updateValue(self::TELEPHONE2, Tools::getValue(self::TELEPHONE2));
            Configuration::updateValue(self::MESSAGE, Tools::getValue(self::MESSAGE));
            Configuration::updateValue(self::HOURS, Tools::getValue(self::HOURS));

            // all ok :)
            $this->context->smarty->assign('confirmation', 'ok');
        }
    }

    /**
     * Get configuration values
     *
     * @return array Configuration values
     */
    public function getConfiguration()
    {
        $configs = array();
        $configs[strtolower(self::SURNAME)] = Configuration::get(self::SURNAME);
        $configs[strtolower(self::EMAIL)] = Configuration::get(self::EMAIL);
        $configs[strtolower(self::TELEPHONE2)] = Configuration::get(self::TELEPHONE2);
        $configs[strtolower(self::MESSAGE)] = Configuration::get(self::MESSAGE);
        $configs[strtolower(self::HOURS)] = Configuration::get(self::HOURS);

        return $configs;
    }

    /**
     * Function to get website url
     *
     * @return string Website URL
     */
    public function getBaseUrl()
    {
        if (_PS_BASE_URL_SSL_) {
            $baseUrl = _PS_BASE_URL_SSL_;
        } else {
            $baseUrl = _PS_BASE_URL_;
        }

        if (__PS_BASE_URI__) {
            $baseUrl .= __PS_BASE_URI__;
        }

        return $baseUrl;
    }

    /**
     * Get translated ajax messages
     *
     * @return array Translated ajax messages
     */
    public function getTranslatedAjaxMessages()
    {
        return array(
            'form_error' => $this->l('Form submission resulted in an error. Please contact the administrator.'),
            'form_error_name' => $this->l('Name field is required.'),
            'form_error_surname' => $this->l('Surname field is required.'),
            'form_error_email' => $this->l('Email field is required.'),
            'form_error_telephone_field' => $this->l('Telephone field is required.'),
            'form_error_telephone' => $this->l('Telephone is not correct, it should be 10 digits.'),
        );
    }

    /**
     * Get country information by IP address.
     *
     * @param string Current user IP
     * @param string Specific data we went returned
     * @param string (Optional) Deep detect
     * @return string|array Returns data based on purpose|Returns all information if purpose is 'Location'
     */
    public function getIPInfo($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
        $output = NULL;
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
        $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
        $continents = array(
            "AF" => "Africa",
            "AN" => "Antarctica",
            "AS" => "Asia",
            "EU" => "Europe",
            "OC" => "Australia (Oceania)",
            "NA" => "North America",
            "SA" => "South America"
        );
        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        $output = array(
                            "city"           => @$ipdat->geoplugin_city,
                            "state"          => @$ipdat->geoplugin_regionName,
                            "country"        => @$ipdat->geoplugin_countryName,
                            "country_code"   => @$ipdat->geoplugin_countryCode,
                            "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                            "continent_code" => @$ipdat->geoplugin_continentCode
                        );
                        break;
                    case "address":
                        $address = array($ipdat->geoplugin_countryName);
                        if (@strlen($ipdat->geoplugin_regionName) >= 1)
                            $address[] = $ipdat->geoplugin_regionName;
                        if (@strlen($ipdat->geoplugin_city) >= 1)
                            $address[] = $ipdat->geoplugin_city;
                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":
                        $output = @$ipdat->geoplugin_city;
                        break;
                    case "state":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "region":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            }
        }
        return $output;
    }

    /**
     * Get country information by IP address.
     *
     * @return string Current user IP.
     */
    public function getUserIP()
    {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                  $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }

        return $ip;
    }
}
