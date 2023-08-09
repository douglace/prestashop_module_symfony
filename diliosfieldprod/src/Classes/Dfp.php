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

namespace Juba\Diliosfieldprod\Classes;

if(!class_exists('DfpProduct'));
    require_once _PS_MODULE_DIR_.'diliosfieldprod/classes/DfpProduct.php';

use Juba\Diliosfieldprod\Repository;
use Db;
use DbQuery;
use DfpProduct;
use Exception;

class Dfp
{

    public static function saveDfp($id_product, $values) {
        try{
            foreach($values as $key=>$languages){
                $product = self::getDfpByKey($key);
                $product->id_product = $id_product;
                $product->key = $key;
                foreach($languages as $id_lang=>$value){
                    $product->value[$id_lang] = $value;
                }
                
                $product->save();
            }
            return true;
        } catch(Exception $e){
            return false;
        }
    }

    /**
     * @param string $key
     * @return DfpProduct
     */
    public static function getDfpByKey($key) {
        $id = Db::getInstance()->getValue('SELECT `id_dfp_product` FROM `' . _DB_PREFIX_ . 'dfp_product` where `key` ="'.$key.'"');
        return new DfpProduct($id);
    }

    /**
     * @param string $key
     * @return array
     */
    public static function getValues($key) {
       $q = new DbQuery();
       $q->select('b.value, b.id_lang')
          ->from('dfp_product', 'a')
          ->innerJoin('dfp_product_lang', 'b', 'a.id_dfp_product = b.id_dfp_product')
          ->where('a.key="'.$key.'"')
       ;
        $values = Db::getInstance()->executeS($q);
        $data = [];
        if(!empty($values)) {
            foreach($values as $value) {
                $data[$value['id_lang']] = $value['value'];
            }
        }
        return $data;
    }
   
}