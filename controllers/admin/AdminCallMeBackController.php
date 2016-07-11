<?php
/**
 * 2015 Sarafoudis Nikolaos for 01generator.com
 *
 * This is a Payment module for Prestashop. This module requires
 *
 *  @author    Sarafoudis Nikolaos for 01generator.com
 *  @copyright Copyright (c) 2015 All Rights Reserved
 *  @license   read license.txt file for more information
 */

class AdminCallMeBackController extends ModuleAdminController
{
    /* Conctructor of the controller */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'callmeback';
        $this->className = 'callmeback';
        $this->list_no_link = true;
        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'a!id',
            ),
            'product_name' => array(
                'title' => $this->l('Product Name'),
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'product_name',
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'a!date_add',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'a!name',
            ),
            'surname' => array(
                'title' => $this->l('Surname'),
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'a!surname',
            ),
            'email' => array(
                'title' => $this->l('Email'),
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'a!email',
            ),
            'telephone_1' => array(
                'title' => $this->l('Telephone'),
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'a!telephone_1',
            ),
            'telephone_2' => array(
                'title' => $this->l('Telephone 2'),
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'a!telephone_2',
            ),
            'message' => array(
                'title' => $this->l('Message'),
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'a!message',
            ),
            'hours' => array(
                'title' => $this->l('Hours to call'),
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'a!hours',
            ),
            'called' => array(
                'title' => $this->l('Called'),
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'a!called',
                'callback' => 'calledStatus',
                'class' => 'calledStatus',
            ),
        );

        // Set the title of the page
        $this->meta_title = $this->l('CallMeBack List');
        $this->toolbar_title[] = $this->meta_title;
        // Call of the parent constructor method
        parent::__construct();

        $this->_select = 't.name as `product_name`';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` t ON (a.`id_product` = t.`id_product`)';
        $this->_where = 'and t.`id_lang` = '.$this->context->language->id;
        $this->_orderBy = 'date_add';
        $this->_orderWay = 'DESC';
    }

    public function calledStatus($id_callmeback, $tr)
    {
        // print_r($tr);
        // die();
        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_ . '/callmeback/views/templates/admin/check-called.tpl');
        $tpl->assign(
            array(
                'id_callmeback' => $tr['id'],
                'called' => $tr['called'],
            )
        );
        return $tpl->fetch();
    }

    /* Loads CSS and JS */
    public function setMedia()
    {
        // We call the parent method
        parent::setMedia();
        // Save the module path in a variable
        $this->path = __PS_BASE_URI__ . 'modules/callmeback/';
        // Include the module CSS and JS files needed
        $this->context->controller->addJS($this->path . 'views/js/callmeback-admin.js');
    }
}
