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

use Juba\Diliosfieldbrand\Repository;
use Juba\Diliosfieldbrand\Classes\Manager;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(_PS_MODULE_DIR_. 'diliosfieldbrand/vendor/autoload.php')) {
    require_once _PS_MODULE_DIR_.  'diliosfieldbrand/vendor/autoload.php';
}

class Diliosfieldbrand extends Module implements WidgetInterface {
    /**
     * @param array $tabs
     */
    public $tabs = [];

    /**
     * @param Juba\Diliosfieldbrand\Repository $repository
     */
    protected $repository;
    
    protected $config_form = "config-global";

    public function __construct()
    {
        $this->name = 'diliosfieldbrand';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Juba';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        $this->tabs = array();

        $this->repository = new Repository($this);

        parent::__construct();
        
        $this->displayName = $this->l('Diliosfieldbrand app');
        $this->description = $this->l('Diliosfieldbrand app description');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->templateFile = 'module:'.$this->name.'/views/templates/hook/dfc-extra-field.tpl';
        
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
    
    public function hookActionAdminCategoriesFormModifier($params)
    {
        $fieldsForm = &$params['fields'];
        
        $fieldsForm[0]['form']['input'][] = array(
            'type' => 'title',
            'label' => $this->l('DFC extra text'),
            'name' => 'dfc_title',
            'autoload_rte' => true,
            'lang' => true
        );

        $fieldsForm[0]['form']['input'][] = array(
            'type' => 'textarea',
            'label' => $this->l('DFC description'),
            'name' => 'dfc_description',
            'autoload_rte' => true,
            'lang' => true
        );

        $id_manufacturer = Tools::getValue('id_manufacturer');
        $image = Manager::getImgPath(false).DIRECTORY_SEPARATOR.$id_manufacturer.'.jpg';
        $image_url = ImageManager::thumbnail(
            $image,
            $this->table.'_'.(int)$id_manufacturer.'.'.$this->imageType,
            350,
            $this->imageType,
            true,
            true
        );
        
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

        $fieldsForm[0]['form']['input'][] = array(
            'type' => 'textarea',
            'label' => $this->l('DFC description'),
            'name' => 'dfc_image',
            'image' => $image_url ? $image_url : false,
            'size' => $image_size,
            'display_image' => true,
            'col' => 6,
        );

        $fieldsValue = &$params['fields_value'];
        $values =$this->getFieldsValues();
        if($values && !empty($values)) {
            $fieldsValue['dfc_title'] = $values['title'];
            $fieldsValue['dfc_description'] = $values['description'];
        }
        
    }

    public function hookActionAdminCategoriesControllerSaveAfter($params)
    {
        $languages = Language::getLanguages(false);
        $shopId = $this->context->shop->id;
        $manufacturerId = (int) Tools::getValue('id_manufacturer');
        $this->uploadImageCategory($manufacturerId);
        foreach ($languages as $lang) {
            $langId = $lang['id_lang'];
            $desc = Tools::getValue('dfc_description_' . $langId);
            $title = Tools::getValue('dfc_title_' . $langId);
            Manager::storeExtrafieldValues($manufacturerId, $desc, $title, $shopId, $langId);
        }
    }

    protected function getFieldsValues()
    {
        $manufacturerId = (int) Tools::getValue('id_manufacturer');
        $languages = Language::getLanguages(false);
        $shopId = $this->context->shop->id;
        $fieldsValues = array();
        foreach ($languages as $lang) {
            $langId = $lang['id_lang'];
            $fieldsValues[$langId] = Manager::getExtrafieldValues($manufacturerId, $shopId, $langId);
        }
        return $fieldsValues;
    }

    public function uploadImageCategory($manufacturer_id) {
        if(isset($_FILES['manufacturer']) && isset($_FILES['manufacturer']['name']['dfc_image'])) {
            
            $tmp_name = $_FILES['manufacturer']['tmp_name']['dfc_image'];
            $error = $_FILES['manufacturer']['error']['dfc_image'];
            
            if($error == UPLOAD_ERR_OK) {
                if(!move_uploaded_file($tmp_name, $this->getImgPath().$manufacturer_id.'.jpg')){
                    return $this->displayError($this->trans('An error occurred while attempting to upload the file.', array(), 'Admin.Notifications.Error'));
                }
            }
        }
    }

    public function getImgPath($front = false){
        return $front ? _MODULE_DIR_.$this->name.'/images/' : __DIR__.'/images/';
    }

    private function updateExtraDescription(array $params)
    {
        $manufacturerId = $params['id'];
        $this->uploadImageCategory($manufacturerId);
        
        $formData = $params['form_data'];
        $shopId = $this->context->shop->id;
        $locales = $this->get('prestashop.adapter.legacy.context')->getLanguages();
        foreach ($locales as $locale) {
            $langId = $locale['id_lang'];
            $desc = $formData['dfc_description'][$langId];
            $title = $formData['dfc_title'][$langId];
            Manager::storeExtrafieldValues(
                $manufacturerId, 
                $desc, 
                $title, 
                $shopId, 
                $langId
            );
        }
    }

    public function hookActionManufacturerFormBuilderModifier(array $params)
    {
        $shopId = $this->context->shop->id;
        $manufacturerId = $params['id'];
        $formBuilder = $params['form_builder'];
        $locales = $this->get('prestashop.adapter.legacy.context')->getLanguages();
        $img_dir = $this->getImgPath(false).$manufacturerId.'.jpg';
        if(Tools::isSubmit('delete-image-extra') && file_exists($img_dir)){
            unlink($this->getImgPath(false).$manufacturerId.'.jpg');
        }
        
        
        $formBuilder->add('dfc_description', TranslateType::class, [
            'type' => FormattedTextareaType::class,
            'label' => $this->getTranslator()->trans('Dfc Extra description', [], 'Modules.bx_extracategorydescription.Admin'),
            'locales' => $locales,
            'hideTabs' => false,
            'required' => false]
        );

        $formBuilder->add('dfc_title', TranslatableType::class, [
            'type' => TextType::class,
            'label' => $this->getTranslator()->trans('Dfc Extra title', [], 'Modules.bx_extracategorydescription.Admin'),
            'locales' => $locales,
            'required' => false]
        );

        $formBuilder->add('dfc_image', FileType::class, [
            'label' => $this->getTranslator()->trans('Dfc Extra image', [], 'Modules.bx_extracategorydescription.Admin'),
            'allow_file_upload' => true,
            'attr' => [
                "data-isset-image"=>file_exists($img_dir) ? "1" : "0",
                "data-image"=>$this->getImgPath(true).$manufacturerId.'.jpg',
                "data-delete-link"=>$manufacturerId ? $this->context->link->getAdminLink('AdminCategories', true, [
                    'route' => 'admin_manufacturers_edit',
                    'manufacturerId' => $manufacturerId,
                    'delete-image-extra' => $manufacturerId,
                ]) : "",
            ],
            'allow_file_upload' => true,
            'required' => false,
            ]
        );

        
        foreach ($locales as $locale) {
            $langId = $locale['id_lang'];
            $values = $manufacturerId ? Manager::getExtrafieldValues($manufacturerId, $shopId, $langId) : "";
            if($values && !empty($values)) {
                $params['data']['dfc_description'][$langId] = $values['description'];
                $params['data']['dfc_title'][$langId] = $values['title'];
               
            }
        }
        //$params['data']['dfc_image'] = $this->getImgPath(true).$manufacturerId.'.jpg';
        
        $formBuilder->setData($params['data']);
    }

    public function hookActionAfterCreateManufacturerFormHandler(array $params)
    {
        $this->updateExtraDescription($params);
    }
    

    public function hookActionAfterUpdateManufacturerFormHandler(array $params)
    {
        $this->updateExtraDescription($params);
    }

    public function hookDisplayFooter($params)
    {
        $shopId = $this->context->shop->id;
        $langId = $this->context->language->id;
        $manufacturerId = (int) Tools::getValue('id_manufacturer');
        
        if ($manufacturerId > 0) {
            $values = Manager::getExtrafieldValues($manufacturerId, $shopId, $langId);
            $img_dir = $this->getImgPath(false).$manufacturerId.'.jpg';
            if($values && !empty($values)) {
                $this->context->smarty->assign([
                    'title' => $values['title'],
                    'imagelink' => file_exists($img_dir) ? $this->getImgPath(true).$manufacturerId.'.jpg': false,
                    'description' => $values['description']
                ]);
                return $this->fetch('module:'.$this->name.'/views/templates/hook/extrafield-manufacturer-footer.tpl');
            }
        }
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        $shopId = $this->context->shop->id;
        $langId = $this->context->language->id;
        $manufacturerId = (int) Tools::getValue('id_manufacturer');
        $p = (int) Tools::getValue('p');
        if ($p > 1) {
            return false;
        }
        $cacheId = $this->name . '|' . $manufacturerId . '|' . $shopId . '|' . $langId;
        if (!$this->isCached($this->templateFile, $cacheId)) {
            $variables = $this->getWidgetVariables();
            if (empty($variables)) {
                return false;
            }
            $this->smarty->assign($variables);
        }
        return $this->fetch($this->templateFile, $cacheId);
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $shopId = $this->context->shop->id;
        $langId = $this->context->language->id;
        $manufacturerId = (int) Tools::getValue('id_manufacturer');
        if ($manufacturerId > 0) {
            $values = Manager::getExtrafieldValues($manufacturerId, $shopId, $langId);
            if ($values && !empty($values)) {
                return array(
                    'dfc_description' => $values['description'],
                    'dfc_title' => $values['title']
                );
            }
        }
        return false;
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('controller') == "AdminManufacturers") {
            return $this->fetch('module:'.$this->name.'/views/templates/admin/manufacturer.tpl');
        }
    }
}