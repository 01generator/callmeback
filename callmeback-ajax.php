<?php
/**
 * 2015 Sarafoudis Nikolaos for 01generator
 *
 * This is a Payment module for Prestashop. This module requires
 *
 *  @author    Sarafoudis Nikolaos for 01generator
 *  @license   MIT License
 */

include_once '../../config/config.inc.php';
include_once '../../init.php';
include_once './callmeback.php';

switch (Tools::getValue('method')) {
    case 'callmebackSubmit':
        $token = Tools::getValue('token');
        $sys_token = Tools::getToken(false);
        $input_errors = array();

        // Get Variables
        $id_product = Tools::getValue('product_id');
        $name = Tools::getValue('callmeback_name');
        $surname = Tools::getValue('callmeback_surname');
        $email = Tools::getValue('callmeback_email');
        $telephone_1 = Tools::getValue('callmeback_telephone');
        $telephone_2 = Tools::getValue('callmeback_telephone2');
        $message = Tools::getValue('callmeback_msg');
        $callmeback_hours_from = Tools::getValue('callmeback_hours_from');
        $callmeback_hours_to = Tools::getValue('callmeback_hours_to');

        // Get enabled fields
        $configs_callmeback_surname = Configuration::get('CALLMEBACK_SURNAME');
        $configs_callmeback_email = Configuration::get('CALLMEBACK_EMAIL');
        $configs_callmeback_telephone2 = Configuration::get('CALLMEBACK_TELEPHONE2');

        $callmebackClass = new callmeback();
        $translated_form_msgs = $callmebackClass->getTranslatedAjaxMessages();
        if (!$id_product) {
            $input_errors[] = $translated_form_msgs['form_error'];
        }

        if (!$name) {
            $input_errors[] = $translated_form_msgs['form_error_name'];
        }

        if ($configs_callmeback_surname && !$surname) {
            $input_errors[] = $translated_form_msgs['form_error_surname'];
        }

        if ($configs_callmeback_email && !$email) {
            $input_errors[] = $translated_form_msgs['form_error_email'];
        }

        if (!$telephone_1) {
            $input_errors[] = $translated_form_msgs['form_error_telephone_field'];
        } else if (!is_numeric($telephone_1)) {
            $input_errors[] = $translated_form_msgs['form_error_telephone'];
        } else if (strlen($telephone_1) != 10) {
            $input_errors[] = $translated_form_msgs['form_error_telephone'];
        }

        if (empty($input_errors)) {
            if ($token === $sys_token) {
                $insert = array(
                    'id_shop' => (int) Context::getContext()->shop->id,
                    'id_product' => Tools::getValue('product_id'),
                    'name' => Tools::getValue('callmeback_name'),
                    'surname' => Tools::getValue('callmeback_surname'),
                    'email' => Tools::getValue('callmeback_email'),
                    'telephone_1' => Tools::getValue('callmeback_telephone'),
                    'telephone_2' => Tools::getValue('callmeback_telephone2'),
                    'message' => Tools::getValue('callmeback_msg'),
                    'hours' => Tools::getValue('callmeback_hours_from') . ' - ' . Tools::getValue('callmeback_hours_to'),
                    'date_add' => date("Y-m-d H:i:s"),
                    'called' => 0,
                );
                $callmebackSQL = Db::getInstance()->insert('callmeback', $insert);
                if ($callmebackSQL) {
                    die(Tools::jsonEncode(array('callmeback_call' => 1)));
                } else {
                    die(Tools::jsonEncode(array('callmeback_call' => 0, 'callmeback_call_html' => 'sql_error', 'sql_error_msg' => $this->l('Ops! There is an SQL Error, please inform the shop. Sorry!'))));
                }
            } else {
                die(Tools::jsonEncode(array('callmeback_call' => 0, 'callmeback_call_html' => 'ajax_error', 'ajax_error_msg' => $this->l('Ops! There is an AJAX Error, please inform the shop. Sorry!'))));
            }
        } else {
            die(Tools::jsonEncode(array('callmeback_call' => 0, 'callmeback_call_html' => 'form_error', 'form_errors' => $input_errors)));
        }
        break;
    case 'updateCalled':
        $callid = Tools::getValue('callid');
        $call_checked = Tools::getValue('callchecked');
        if ($call_checked == 'true') {
            $call_checked = true;
        } else {
            $call_checked = false;
        }
        // $sys_token = Tools::getToken(false);
        // if ($token === $sys_token) {
        $updatestatement = Db::getInstance()->update(
            'callmeback',
            array(
                'called' => $call_checked,
            ),
            'id=' . $callid
        );
        die(Tools::jsonEncode(array('callupdated' => $updatestatement)));
    // break;
    case 'notifyCallmeback':
        $notifysql = 'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'callmeback WHERE called = 0';
        $notifysqlnu = Db::getInstance()->getValue($notifysql);
        die(Tools::jsonEncode(array('callmeback_notify' => $notifysqlnu)));
    default:
        break;
}
