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

class ArticleModel extends ObjectModel
{
    /**
     * Défini si le produits est actif ou non
     * @param bool $active
     */
    public $active  = true;

     /**
     * Titre de l'article
     * @param int $title
     */
    public $title;

    /**
     * Description de l'article
     * @param string $description
     */
    public $description;

    /**
     * Date ajout
     * @param string $date_add
     */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'dfa_articles',
        'primary' => 'id_dfa_article',
        'multilang' => true,
        'fields' => array(
            'active' => array(
                'type' => self::TYPE_BOOL
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'
            ),
            'title' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'lang' => true,
                'required' => true,
                'size' => 255
            ),
            'description' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHTML'
            ),
        ),
    );
}