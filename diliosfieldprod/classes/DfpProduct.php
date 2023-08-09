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

class DfpProduct extends ObjectModel
{
    /**
     * Défini si le produits est actif ou non
     * @param int $id_product
     */
    public $id_product;

     /**
     * Titre de l'article
     * @param string $key
     */
    public $key;

    /**
     * value de l'article
     * @param string $value
     */
    public $value;
    

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'dfp_product',
        'primary' => 'id_dfp_product',
        'multilang' => true,
        'fields' => array(
            'id_product' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'key' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'value' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHtml',
                'lang' => true,
            ),
        ),
    );
    
}