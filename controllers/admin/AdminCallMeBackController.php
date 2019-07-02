<?php
/**
 * 2015 Sarafoudis Nikolaos for 01generator
 *
 * This is a Payment module for Prestashop. This module requires
 *
 *  @author    Sarafoudis Nikolaos for 01generator
 *  @license   MIT License
 */

/**
 * Class AdminCallMeBackController
 */
class AdminCallMeBackController extends ModuleAdminController
{
    /**
     * AdminCallMeBackController constructor.
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'callmeback';
        $this->className = 'callmeback';
        $this->identifier = 'id';
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
                'active' => 'toggleCalled',
                'align' => 'text-center',
                'havingFilter' => true,
                'type' => 'bool',
                'class' => 'calledStatus',
            ),
        );

        // Set the title of the page
        $this->meta_title = $this->l('CallMeBack List');
        $this->toolbar_title[] = $this->meta_title;
        // Call of the parent constructor method
        parent::__construct();

        $this->_select = 't.name as `product_name`';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_lang` t ON (a.`id_product` = t.`id_product`)';
        $this->_where = 'AND t.`id_lang` = '.$this->context->language->id.' AND t.`id_shop`=a.`id_shop`';
        $this->_orderBy = 'date_add';
        $this->_orderWay = 'DESC';
    }

    /**
     * Implements postProcess()
     */
    public function postProcess()
    {
        
        if (Tools::getIsset('toggleCalled'.$this->table)) {
            $this->toggleArchived();
        }
        parent::postProcess();
    }

    /**
     * Helper function that updates Called status of row.
     */
    public function toggleArchived()
    {   
        $id = Tools::getValue('id');
        $query = "SELECT `called` FROM " . _DB_PREFIX_ . "callmeback WHERE `id`=" . $id;
        $is_called = Db::getInstance()->getValue($query);

        // Check called value and change it.
        $is_called == 0 ? $data = array('called' => 1) : $data = array('called' => 0);

        $where = '`id`=' . $id;
        Db::getInstance()->update('callmeback', $data, $where);
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

}
