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

use Db;
use Tab;
use Module;
use Language;

class Installer 
{
    public $tabs = [ 
        [ 
            'class_name' => 'AdminYatPushCategory', 
            'parent_class_name' => 'DEFAULT', 
            'icon' => '', 
            'name' => 'Yateo', 
            'icon' => 'book', 
            'wording' => 'Yateo', 
            'wording_domain' => 'Modules.Yatpush.Admin', 
        ], 
        [ 
            'class_name' => 'AdminYatPushMenCategory', 
            'parent_class_name' => 'AdminYatPushCategory', 
            'icon' => '', 
            'name' => 'Push man',  
            'icon' => 'list', 
            'wording' => 'Push men', 
            'wording_domain' => 'Modules.Yatpush.Admin', 
        ], 
        [ 
            'class_name' => 'AdminYatPushWomenCategory', 
            'parent_class_name' => 'AdminYatPushCategory', 
            'icon' => 'book', 
            'name' => 'Push woman', 
            'icon' => 'list', 
            'wording' => 'Push women', 
            'wording_domain' => 'Modules.Yatpush.Admin', 
        ], 
    ];

    /**
     * Déclenche l'installation du module
     * @param Module $module
     * @return bool
     */
    public function install(Module $module): bool
    {
        if (!$this->registerHooks($module)) {
            return false;
        }

        if (!$this->installTab($module)) {
            return false;
        }

        if (!$this->installDatabase()) {
            return false;
        }

        return true;
    }

    /**
     * Désinstalle le module
     * @return bool
     */
    public function uninstall(): bool
    {
        return $this->uninstallDatabase() && $this->unInstallTab();
    }

    /**
     * Install les tables du module
     * @return bool
     */
    public function installDatabase(): bool
    {
        return $this->executeQueries(Database::installQueries());
    }

    /**
     * Désinstall les tables du module
     * @return bool
     */
    private function unInstallDatabase(): bool
    {
        return $this->executeQueries(Database::unInstallQueries());
    }


    /**
     * Accroche le module sur le hook
     * @param Module $module
     * @return bool
     */
    public function registerHooks(Module $module): bool
    {
        $hooks = [
            'displayHomeMen',
            'displayHomeWomen',
            'displayBackOfficeHeader',
        ];

        return (bool) $module->registerHook($hooks);
    }

    /**
     * Execute les requêtes SQL
     * @param array $queries
     * @return bool
     */
    private function executeQueries(array $queries): bool
    {
        if(empty($queries)) return true;

        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Installer un nouvelle onglet en admin
     */
    protected function installTab(Module $module)
    {
        $languages = Language::getLanguages();

        foreach ($this->tabs as $t) {
            $exist = Tab::getIdFromClassName($t['class_name']);
            if(!$exist) { 
                $tab = new Tab();
                $tab->active = true; 
                $tab->enabled = true; 
                $tab->module = $module->name;
                $tab->class_name = $t['class_name'];
                $tab->id_parent = (int)Tab::getInstanceFromClassName($t['parent_class_name'])->id;

                foreach ($languages as $language) {
                    $tab->name[$language['id_lang']] = $t['name'];
                }

                $tab->icon = $t['icon']; 
                $tab->wording = $t['wording'];
                $tab->wording_domain = $t['wording_domain']; 
                $tab->save();
            }
            
        }
        return true;
    }

    /**
     * Installer un nouvelle onglet en admin
     */
    protected function unInstallTab()
    {
        foreach ($this->tabs as $t) {
            $id = Tab::getIdFromClassName($t['class_name']);
            if ($id) {
                $tab = new Tab($id);
                $tab->delete();
            }
        }
        
        return true;
    }

}