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

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(_PS_MODULE_DIR_. 'diliosfieldprod/vendor/autoload.php')) {
    require_once _PS_MODULE_DIR_.  'diliosfieldprod/vendor/autoload.php';
}

use Juba\Diliosfieldprod\Classes\Dfp;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
class Diliosfieldprod extends Module {
    /**
     * @param array $tabs
     */
    public $tabs = [];

    /**
     * @param Juba\Diliosfieldprod\Repository $repository
     */
    protected $repository;
    
    protected $config_form = "config-global";

    public function __construct()
    {
        $this->name = 'diliosfieldprod';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Juba';
        $this->need_instance = 0;
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;
        

        $this->repository = new Juba\Diliosfieldprod\Repository($this);

        parent::__construct();
        
        

        $this->displayName = $this->l('Diliosfieldprod');
        $this->description = $this->l('Diliosfieldprod description');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() && $this->repository->install();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->repository->uninstall();
    }

    

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        if(Tools::getValue('module') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/article.js');
            $this->context->controller->addCSS($this->_path.'views/css/article.css');
        }
    }

    public function hookDisplayAdminProductsExtra($params) {
        return $this->getForm(
            $params['id_product'],
            'Display adminproduct extrat',
            'dpae'
        );
    }

    public function hookDisplayAdminProductsCombinationBottom($params) {
        return $this->getForm(
            $params['id_product'],
            'Display adminproduct combination bottom',
            'dpcb'
        );
    }

    public function hookDisplayAdminProductsMainStepLeftColumnBottom($params) {
        return $this->getForm(
            $params['id_product'],
            'Display adminproduct main step left column bottom',
            'dpmslcb'
        );
    }

    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params) {
        return $this->getForm(
            $params['id_product'],
            'Display adminproduct main step left column midlle',
            'dpmslcm'
        );
    }

    public function hookDisplayAdminProductsMainStepRightColumnBottom($params) {
        return $this->getForm(
            $params['id_product'],
            'Display adminproduct main step right column bottom',
            'dpmsrcb'
        );
    }

    public function hookDisplayAdminProductsOptionsStepTop($params) {
        return $this->getForm(
            $params['id_product'],
            'Display adminproduct options step top',
            'dpost'
        );
    }

    public function hookDisplayAdminProductsPriceStepBottom($params) {
        return $this->getForm(
            $params['id_product'],
            'Display adminproduct price step bottom',
            'dppsb'
        );
    }

    public function hookDisplayAdminProductsQuantitiesStepBottom($params) {
        return $this->getForm(
            $params['id_product'],
            'Display adminproduct quantities step bottom',
            'dpqsb'
        );
    }

    public function hookDisplayAdminProductsSeoStepBottom($params) {
        return $this->getForm(
            $params['id_product'],
            'Display adminproduct seo step bottom',
            'dpseosb'
        );
    }

    public function hookDisplayAdminProductsShippingStepBottom($params) {
        return $this->getForm(
            $params['id_product'],
            'Display adminproduct shipping step bottom',
            'dpssb'
        );
    }

    public function hookDisplayAdminProductsOptionsStepBottom($params) {
        return $this->getForm(
            $params['id_product'],
            'Display adminproduct Option Step Bottom',
            'dposb'
        );
    }

    public function hookActionAdminProductsControllerSaveBefore($params) {
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        $product = $productAdapter->getProduct($_REQUEST['form']['id_product']);
        $data = array();
        $keys = [
            'dpae',
            'dpcb',
            'dpmslcb',
            'dpmslcm',
            'dpmsrcb',
            'dpost',
            'dppsb',
            'dpseosb',
            'dpssb',
            'dposb',
        ];

        try {
            $form = $_REQUEST['form'];
            foreach($keys as $key){
                $title_field = $key.'_dfp_title';
                $description_field = $key.'_dfp_description';
                /*$image_field = $key.'_dfp_image';
                if(isset($_REQUEST['form'][$image_field])){
                    $image = $_REQUEST['form'][$image_field]->getData();
                    dump($image);
                }*/
                
                foreach(Language::getLanguages() as $language){
                    if(isset($form[$title_field])) {
                        $data_title =  $form[$title_field];
                        $data[$title_field][$language['id_lang']] = $data_title[$language['id_lang']];
                    }
                    if(isset($form[$description_field])) {
                        $data_description = $form[$description_field];
                        $data[$description_field][$language['id_lang']] = $data_description[$language['id_lang']];
                    }
                }
            }
            
            if(!empty($data))
                Dfp::saveDfp($product->id, $data);

        } catch(Exception $e) {
            throw "And error occured";
        }

        
    }

    public function getImgPath($front = false){
        return $front ? _MODULE_DIR_.$this->name.'/images/' : __DIR__.'/images/';
    }

    public function getForm($id_product, $title, $key = "dfc") {
        
        //$productAdapter = $this->get('prestashop.adapter.data_provider.product');
        //$product = $productAdapter->getProduct($id_product);
        $locales = $this->get('prestashop.adapter.legacy.context')->getLanguages();
        //$languages = Language::getLanguages();
        $title_field = $key.'_dfp_title';
        $description_field = $key.'_dfp_description';
        //$image_field = $key.'_dfp_image';
        //$img_dir = $this->getImgPath(false).$key.'_'.$id_product.'.jpg';
        //$img_url = $this->getImgPath(true).$key.'_'.$id_product.'.jpg';
        
        $formData = [
            $title_field => Dfp::getValues($title_field),
            $description_field => Dfp::getValues($description_field),
        ];

        $formFactory = $this->get('form.factory');
        $formBuilder = $formFactory->createBuilder(FormType::class, $formData);
        
        $formBuilder->add($title_field, TranslateType::class, [
            'type' => TextType::class,
            'label' => $this->getTranslator()->trans('Dfc Extra title', [], 'Modules.bx_extracategorydescription.Admin'),
            'locales' => $locales,
            'required' => false
        ]);

        $formBuilder->add($description_field, TranslateType::class, [
            'type' => FormattedTextareaType::class,
            'label' => $this->getTranslator()->trans('Dfc Extra description', [], 'Modules.bx_extracategorydescription.Admin'),
            'locales' => $locales,
            'hideTabs' => false,
            'required' => false
        ]);
        
        /*$formBuilder->add($image_field, FileType::class, [
            'label' => $this->getTranslator()->trans('Dfc Extra image', [], 'Modules.bx_extracategorydescription.Admin'),
            'allow_file_upload' => true,
            'attr' => [
                "data-isset-image"=>file_exists($img_dir) ? "1" : "0",
                "data-image"=>$img_url,
                "data-delete-link"=>$id_product ? $this->context->link->getAdminLink('AdminCategories', true, [
                    'route' => 'admin_categories_edit',
                    'categoryId' => $id_product,
                    'delete-image-extra' => $id_product,
                ]) : "",
            ],
            'allow_file_upload' => true,
            'required' => false,
            ]
        );*/

        $form = $formBuilder->getForm();
        
        
        return $this->get('twig')->render(_PS_MODULE_DIR_.$this->name.'/views/templates/hook/display-admin-products-extra.html.twig', [
            'form' => $form->createView(),
            'key' => $key,
            'title'=> $title,
            'title_field'=> $title_field,
            'description_field'=> $description_field,
            //'image_field'=> $image_field,
        ]) ;

    }

    public function hookActionOrderGridQueryBuilderModifier($params) {
        //dump($params);die;
    }
}