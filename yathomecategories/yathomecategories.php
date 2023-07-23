<?php
/**
* 2007-2023 PrestaShop
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
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
declare(strict_types=1); 

use Yateo\Yathomecategories\Repository\CategoryRepository;
use Yateo\Yathomecategories\Install\Installer;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__.'/vendor/autoload.php';

class Yathomecategories extends Module 
{
    public function __construct()
    {
        $this->name = 'yathomecategories';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'yateo';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->languages = Language::getLanguages();

        $this->displayName = $this->l('Affiche les catégories sur la home page');
        $this->description = $this->l('Ce module permet d\'afficher les catégories sur la home ');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        $installer = new Installer();

        return $installer->install($this);
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        $installer = new Installer();

        return $installer->uninstall($this);
    }

    /** Utilisation de la traduction moderne de prestashop */
    public function isUsingNewTranslationSystem() 
    { 
        return true; 
    }

    public function hookDisplayHome($params)
    {
        /** @var CategoryRepository */
        $repository = $this->get('yhc.repository.category.repository');
        $type = "MAN";
        $id_lang = $this->context->language->id;
        $yatcontext = Module::getInstanceByName('yatmenucontext');
        if($yatcontext && $yatcontext->getSiteContext() == "CONTEXT_FEMME")
        {
            $type = "WOMAN";
            $allLink = Configuration::get('YHC_CATEGORY_WOMEN_LINK', $id_lang); 
        } else {
            $allLink = Configuration::get('YHC_CATEGORY_MEN_LINK', $id_lang); 
        }
        $items = $repository->getItems($type, $id_lang);
        $this->context->smarty->assign('items', $items);
        $this->context->smarty->assign('allLink', $allLink);

        return $this->fetch('module:'.$this->name.'/views/templates/hook/home.tpl');
    }

    public function getContent()
    {
        Tools::redirectAdmin(
            SymfonyContainer::getInstance()
            ->get('router')
            ->generate('yhc_men')
        );
    }
}
