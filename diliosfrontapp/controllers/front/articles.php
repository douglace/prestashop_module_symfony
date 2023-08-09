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


class diliosfrontappArticlesModuleFrontController extends ModuleFrontController {
    public $display_column_left = false;

    /**
     * @var ArticleModel
     */
    public $article = null;

    /**
     * @var []
     */
    public $articles = [];

    public function init(){
        parent::init();

        if($id_article = Tools::getValue('id_article')) {
            $this->article = new ArticleModel($id_article, $this->context->language->id);
            if(!Validate::isLoadedObject($this->article)) {
                Tools::redirect($this->context->link->getPageLink('404'));
            }
        } else {
            $this->articles = Article::getArticles();
        }
    }

    public function initContent(){
        parent::initContent();
        if($this->article) {
            $this->context->smarty->assign(array(
                'article' => $this->article,
                'article_image'=>Article::getImgPath(true).'/'.$this->article->id.'.jpg',
            ));
            $this->setTemplate('module:diliosfrontapp/views/templates/front/article.tpl');
        } else {
            $this->context->smarty->assign(array(
                'articles' => $this->articles
            ));
            $this->setTemplate('module:diliosfrontapp/views/templates/front/articles.tpl');
        }
        
        

    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [   
            'title' => $this->l('Articles'),
            'url' => $this->context->link->getModuleLink(
                $this->module->name, 'articles'
            )
        ];

        if($this->article){
            $breadcrumb['links'][] = [   
                'title' => $this->article->title,
                'url' => $this->context->link->getModuleLink(
                    $this->module->name, 'articles'
                )
            ];
        }
        return $breadcrumb;
    }
}