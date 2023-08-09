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

namespace Juba\Diliosfrontapp\Classes;

use Juba\Diliosfrontapp\Repository;
use PrestaShopDatabaseException;
use ObjectModel;
use Context;
use DbQuery;
use Db;

class Article extends ObjectModel
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
     *
     * @param int $limit
     * @param int $offset
     * @param int $id_lang
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     *
     * @throws PrestaShopDatabaseException
     */
    public static function getArticles($limit = 0, $offset = 0, $id_lang = null) {
        $id_lang = $id_lang ? $id_lang : Context::getContext()->language->id;

        $q = new DbQuery();
        $q->select('a.*, b.*')
            ->from('dfa_articles', 'a')
            ->innerJoin('dfa_articles_lang', 'b', 'b.id_dfa_article=a.id_dfa_article')
            ->where('b.id_lang='.$id_lang);
        
        if((int)$limit) {
            $q->limit($limit, $offset);
        }
        $link = Context::getContext()->link;
        return array_map(function($i)use($link){
            $i['link'] = $link->getModuleLink('diliosfrontapp', 'articles', ['id_article' => $i['id_dfa_article']]);
            $i['img'] = self::getImgPath(true).'/'.$i['id_dfa_article'].'.jpg';
            return $i;
        }, Db::getInstance()->executeS($q));
    }

   
}