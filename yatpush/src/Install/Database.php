<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

declare(strict_types=1);

namespace Yateo\Yatpush\Install;

class Database 
{

    /**
     * Liste des tables à installer
     * @return array
     */
    public static function installQueries(): array
    {
        $queries = [];

        $queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'yateo_push` (
            `id_yateo_push` int(11) NOT NULL AUTO_INCREMENT,
            `position` int(11) NOT NULL DEFAULT 0,
            `active` int(11) NOT NULL DEFAULT 1,
            `type` ENUM("MAN","WOMAN") DEFAULT "MAN",
            PRIMARY KEY (`id_yateo_push`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        $queries[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'yateo_push_lang` (
            `id_yateo_push` int(11) NOT NULL AUTO_INCREMENT,
            `id_lang` int(11) NOT NULL,
            `link` VARCHAR(250) NULL,
            `text_top` VARCHAR(250) NULL,
            `text_bottom` VARCHAR(250) NULL,
            `text_button` VARCHAR(250) NULL,
            PRIMARY KEY (`id_yateo_push`, `id_lang`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        

        return $queries;
    }

    /**
     * Requêtes de désinstallation
     * @return array
     */
    public static function uninstallQueries(): array
    {
        $queries = [];

        $queries[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'yateo_push`';
        $queries[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'yateo_push_lang`';
        
        return $queries;
    }

}