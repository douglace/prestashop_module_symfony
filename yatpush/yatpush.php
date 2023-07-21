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

use Yateo\Yatpush\Install\Installer;
use Yateo\Yatpush\Repository\PushRepository;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__.'/vendor/autoload.php';

class Yatpush extends Module 
{
    public function __construct()
    {
        $this->name = 'yatpush';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Yateo';
        $this->need_instance = 0;  
        
        $this->bootstrap = true;

        parent::__construct();
        
        $this->displayName = $this->l('Ajoute un listing d\'article');
        $this->description = $this->l('Ce module ajoute un listing d\'article');
        $this->ps_versions_compliancy = array('min' => '1.7.0', 'max' => _PS_VERSION_);
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
        $installer = new Installer();
        return $installer->uninstall() && parent::uninstall();
    }

    /** Utilisation de la traduction moderne de prestashop */
    public function isUsingNewTranslationSystem() 
    { 
        return true; 
    } 

    public function hookDisplayHomeMen($params)
    {
        /** @var PushRepository */
        $repository = $this->get('yatpush.repository.push.repository');
        return $this->displayItems([
            'type' => "HOMME",
            'items' => $repository->getItems('MAN', $this->context->language->id)
        ]);
    }

    public function hookDisplayHomeWomen($params)
    {
        /** @var PushRepository */
        $repository = $this->get('yatpush.repository.push.repository');
        return $this->displayItems([
            'type' => "FEMME",
            'items' => $repository->getItems('WOMAN', $this->context->language->id)
        ]);
    }

    public function displayItems($data)
    {
        $this->context->smarty->assign($data);
        return $this->fetch('module:'.$this->name.'/views/templates/hook/display.tpl');
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        return "";
    }

    public function getContent()
    {
        Tools::redirectAdmin(
            SymfonyContainer::getInstance()
            ->get('router')
            ->generate('yatpush_pushmen')
        );
    }
}