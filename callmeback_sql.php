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

class CallMeBackSQL
{
    public function createTables()
    {
        if (!Db::getInstance()->Execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'callmeback` (
                `id` int(10) NOT NULL AUTO_INCREMENT,
                `id_shop` int(11) unsigned NOT NULL DEFAULT \'1\',
                `id_product` int(11) unsigned NOT NULL DEFAULT \'1\',
                `name` varchar(255) NOT NULL,
                `surname` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `telephone_1` varchar(255) NOT NULL,
                `telephone_2` varchar(255) NOT NULL,
                `message` text NOT NULL,
                `hours` varchar(255) NOT NULL,
                `date_add` datetime NOT NULL,
                `called` tinyint(1) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `id_shop` (`id_shop`),
                KEY `id_product` (`id_product`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1')) {
            return false;
        }

    }
}
