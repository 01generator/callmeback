<?php

/**
 * Class CallMeBackAjaxModuleFrontController
 */
class CallMeBackAjaxModuleFrontController extends ModuleFrontController
{
    /**
     * Initialize ModuleFrontController content
     *
     * @throws PrestaShopDatabaseException
     */
    public function initContent()
    {
        switch (Tools::getValue('method')) {
            case 'callmebackSubmit':
                $token = Tools::getValue('token');
                $systemToken = Tools::getToken(false);
                $inputErrors = array();

                // Get Variables
                $idProduct = Tools::getValue('product_id');
                $name = Tools::getValue('callmeback_name');
                $surname = Tools::getValue('callmeback_surname');
                $email = Tools::getValue('callmeback_email');
                $telephone1 = Tools::getValue('callmeback_telephone');
                $telephone2 = Tools::getValue('callmeback_telephone2');
                $message = Tools::getValue('callmeback_msg');
                $callMeBackHoursFrom = Tools::getValue('callmeback_hours_from');
                $callMeBackHoursTo = Tools::getValue('callmeback_hours_to');

                // Get enabled fields
                $configsCallMeBackSurname = Configuration::get('CALLMEBACK_SURNAME');
                $configsCallMeBackEmail = Configuration::get('CALLMEBACK_EMAIL');
                $configsCallMeBackTelephone2 = Configuration::get('CALLMEBACK_TELEPHONE2');

                $callmebackClass = new callmeback();
                $translatedFormessages = $callmebackClass->getTranslatedAjaxMessages();
                if (!$idProduct) {
                    $inputErrors[] = $translatedFormessages['form_error'];
                }

                if (!$name) {
                    $inputErrors[] = $translatedFormessages['form_error_name'];
                }

                if ($configsCallMeBackSurname && !$surname) {
                    $inputErrors[] = $translatedFormessages['form_error_surname'];
                }

                if ($configsCallMeBackEmail && !$email) {
                    $inputErrors[] = $translatedFormessages['form_error_email'];
                }

                // if (!$telephone1) {
                //     $inputErrors[] = $translatedFormessages['form_error_telephone_field'];
                // } elseif (!is_numeric($telephone1)) {
                //     $inputErrors[] = $translatedFormessages['form_error_telephone'];
                // } elseif (strlen($telephone1) != 10) {
                //     $inputErrors[] = $translatedFormessages['form_error_telephone'];
                // }

                if (empty($inputErrors)) {
                    if ($token === $systemToken) {
                        $insert = array(
                            'id_shop' => (int) Context::getContext()->shop->id,
                            'id_product' => Tools::getValue('product_id'),
                            'name' => Tools::getValue('callmeback_name'),
                            'surname' => Tools::getValue('callmeback_surname'),
                            'email' => Tools::getValue('callmeback_email'),
                            'telephone_1' => Tools::getValue('callmeback_telephone'),
                            'telephone_2' => Tools::getValue('callmeback_telephone2'),
                            'message' => Tools::getValue('callmeback_msg'),
                            'hours' => Tools::getValue('callmeback_hours_from').' - '.Tools::getValue('callmeback_hours_to'),
                            'date_add' => date("Y-m-d H:i:s"),
                            'called' => 0,
                        );
                        $callmebackSQL = Db::getInstance()->insert('callmeback', $insert);
                        if ($callmebackSQL) {
                            die(Tools::jsonEncode(array('callmeback_call' => 1)));
                        } else {
                            die(Tools::jsonEncode(array('callmeback_call' => 0, 'callmeback_call_html' => 'sql_error', 'sql_error_msg' => $this->l('Oops! There is an SQL Error, please inform the shop. Sorry!'))));
                        }
                    } else {
                        die(Tools::jsonEncode(array('callmeback_call' => 0, 'callmeback_call_html' => 'ajax_error', 'ajax_error_msg' => $this->l('Oops! There is an AJAX Error, please inform the shop. Sorry!'))));
                    }
                } else {
                    die(Tools::jsonEncode(array('callmeback_call' => 0, 'callmeback_call_html' => 'form_error', 'form_errors' => $inputErrors)));
                }
                break;
            case 'updateCalled':
                $callid = Tools::getValue('callid');
                $callChecked = Tools::getValue('callchecked');
                if ($callChecked == 'true') {
                    $callChecked = true;
                } else {
                    $callChecked = false;
                }
                $updatestatement = Db::getInstance()->update(
                    'callmeback',
                    array(
                        'called' => $callChecked,
                    ),
                    'id='.$callid
                );
                die(Tools::jsonEncode(array('callupdated' => $updatestatement)));
            case 'notifyCallmeback':
                $notifysql = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.'callmeback` WHERE called = 0';
                $notifysqlnu = Db::getInstance()->getValue($notifysql);
                die(Tools::jsonEncode(array('callmeback_notify' => $notifysqlnu)));
            default:
                break;
        }
    }
}