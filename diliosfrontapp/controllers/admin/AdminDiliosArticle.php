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

if(!class_exists('ArticleModel'));
    require_once _PS_MODULE_DIR_.'diliosfrontapp/classes/ArticleModel.php';

use Juba\Diliosfrontapp\Classes\Article;

class AdminDiliosArticleController extends ModuleAdminController {

    public function __construct()
    {
        $this->table = 'dfa_articles';
        $this->className = 'ArticleModel';
        $this->lang = true;
        $this->bootstrap = true;

        $this->deleted = false;
        $this->allow_export = true;
        $this->list_id = 'articles';
        $this->identifier = 'id_dfa_article';
        $this->_defaultOrderBy = 'id_dfa_article';
        $this->_defaultOrderWay = 'ASC';
        $this->context = Context::getContext();

        $this->addRowAction('edit');
        $this->addRowAction('delete'); 
        
        $this->fieldImageSettings = array(
            'name' => 'avatar',
            'dir' => 'diliosfrontapp'
        );

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->module->getTranslator()->trans('Delete selected', [], 'Modules.Diliosfrontapp.Diliosarticle'),
                'icon' => 'icon-trash',
                'confirm' => $this->module->getTranslator()->trans('Delete selected items?', [], 'Modules.Diliosfrontapp.Diliosarticle')
            )
        );

        $this->fields_list = array(
            'id_dfa_article'=>array(
                'title' => $this->module->getTranslator()->trans('ID', [], 'Modules.Diliosfrontapp.Diliosarticle'),
                'align'=>'center',
                'class'=>'fixed-width-xs'
            ),
            'avatar' => array(
                'title' => $this->module->getTranslator()->trans('Profile', [], 'Modules.Diliosfrontapp.Diliosarticle'),
                'image' => 'diliosfrontapp',
                'orderby' => false,
                'search' => false,
                'align' => 'center',
            ),
            'title'=>array(
                'title'=>$this->module->getTranslator()->trans('Title', [], 'Modules.Diliosfrontapp.Diliosarticle'),
                'width'=>'auto'
            ),
            'active' => array(
                'title' => $this->module->getTranslator()->trans('Enabled', [], 'Modules.Diliosfrontapp.Diliosarticle'),
                'active' =>'status',
                'type' =>'bool',
                'align' =>'center',
                'class' =>'fixed-width-xs',
                'orderby' => false,
            )
        );
    }
    

    public function renderForm()
    {
        if (!($article = $this->loadObject(true))) {
            return;
        }

        $image = Article::getImgPath(false).DIRECTORY_SEPARATOR.$article->id.'.jpg';
        $image_url = ImageManager::thumbnail(
            $image,
            $this->table.'_'.(int)$article->id.'.'.$this->imageType,
            350,
            $this->imageType,
            true,
            true
        );
        
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;
        
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->module->getTranslator()->trans('Dilios Article', [], 'Modules.Diliosfrontapp.Diliosarticle'),
                'icon' => 'icon-certificate'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Title', [], 'Modules.Diliosfrontapp.Diliosarticle'),
                    'name' => 'title',
                    'lang'=>true,
                    'col' => 4,
                    'required' => true,
                    'hint' => $this->module->getTranslator()->trans('Invalid characters:', [], 'Modules.Diliosfrontapp.Diliosarticle').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->getTranslator()->trans('Description', [], 'Modules.Diliosfrontapp.Diliosarticle'),
                    'name' => 'description',
                    'autoload_rte' => true,
                    'lang' => true,
                    'cols' => 60,
                    'rows' => 10,
                    'col' => 8,
                    'hint' => $this->module->getTranslator()->trans('Invalid characters:', [], 'Modules.Diliosfrontapp.Diliosarticle').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'file',
                    'label' => $this->module->getTranslator()->trans('Image', [], 'Modules.Diliosfrontapp.Diliosarticle'),
                    'name' => 'avatar',
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'display_image' => true,
                    'col' => 6,
                    'hint' => $this->module->getTranslator()->trans('Upload a testimony logo from your computer.', [], 'Modules.Diliosfrontapp.Diliosarticle')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->getTranslator()->trans('Enable', [], 'Modules.Diliosfrontapp.Diliosarticle'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->getTranslator()->trans('Enabled', [], 'Modules.Diliosfrontapp.Diliosarticle')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->getTranslator()->trans('Disabled', [], 'Modules.Diliosfrontapp.Diliosarticle')
                        )
                    )
                )
            )
        );

        if (!($article = $this->loadObject(true))) {
            return;
        }


        $this->fields_form['submit'] = array(
            'title' => $this->module->getTranslator()->trans('Save', [], 'Modules.Diliosfrontapp.Diliosarticle')
        );
        

        return parent::renderForm();
    }
}