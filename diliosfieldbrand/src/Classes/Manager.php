<?php
/**
* 2007-2022 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace Juba\diliosfieldbrand\Classes;

use Juba\diliosfieldbrand\Repository;

use PrestaShopDatabaseException;
use ObjectModel;
use Context;
use DbQuery;
use Db;

class Manager extends ObjectModel
{
    /**
     * Retourne le lien ou le repertoire de l'image
     * @param bool $front
     * @return string
     */
    public static function getImgPath($front = false){
        return $front ? Repository::$img_front : Repository::$img_dir;
    }

    /**
     * @param int $shopId
     * @param int $langId
     * @param int $manufacturerId
     * @param string $desc
     * @param string $title
     */
    public static function storeExtrafieldValues($manufacturerId, $desc, $title, $shopId = null, $langId = null)
    {
        $langId  = $langId ? $langId : Context::getContext()->language->id;
        $shopId  = $shopId ? $shopId : Context::getContext()->shop->id;

        if (self::exists($manufacturerId, $shopId, $langId)) {
            Db::getInstance()->update(
                Repository::$extra_desc_table,
                array(
                    'description' => pSQL($desc, true),
                    'title' => pSQL($title),
                ),
                '`id_manufacturer` = ' . $manufacturerId . ' '.
                ' AND `id_shop` = ' . $shopId . ' AND `id_lang` = ' . $langId
            );
        } else {
            Db::getInstance()->insert(
                Repository::$extra_desc_table,
                array(
                    'description' => pSQL($desc, true),
                    'title' => pSQL($title),
                    'id_manufacturer' => $manufacturerId,
                    'id_shop' => $shopId,
                    'id_lang' => $langId
                )
            );
        }
    }

    /**
     * @param int $manufacturerId
     * @param int $shopId
     * @param int $langId
     * @return string|false Returns false if no results
     */
    public static function exists($manufacturerId, $shopId, $langId)
    {
        $q = new DbQuery();
        $q->select('1')
            ->from(Repository::$extra_desc_table)
            ->where('id_manufacturer='.$manufacturerId)
            ->where('id_lang='.$langId)
            ->where('id_shop='.$shopId)
        ;
        return (bool)Db::getInstance()->getValue($q);
    }

    /**
     * @param int $manufacturerId
     * @param int $shopId
     * @param int $langId
     * @return array|bool|object|null
     */
    public static function getExtrafieldValues($manufacturerId, $shopId, $langId)
    {
        $q = new DbQuery();
        $q->select('`title`, `description`')
            ->from(Repository::$extra_desc_table)
            ->where('id_manufacturer='.$manufacturerId)
            ->where('id_lang='.$langId)
            ->where('id_shop='.$shopId)
        ;
        return Db::getInstance()->getRow($q);
    }
}