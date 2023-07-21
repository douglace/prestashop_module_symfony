<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);

namespace Yateo\Yathomecategories\Form\Type;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\CustomContentType;
use Symfony\Component\Form\FormBuilderInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yateo\Yathomecategories\Uploader\YatCategoryImageUploader;

class YHCType extends TranslatorAwareType
{

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        parent::buildForm($builder, $options);
        $builder
            ->add('imagestimes', HiddenType::class, [])
            ->add('type', HiddenType::class, [])
            ->add('link', TranslatableType::class, [
                'type' => TextType::class,
                'required' => true,
                'label'    => $this->trans('Lien', 'Modules.Yathomecategories.Admin')
            ])
            ->add('title', TranslatableType::class, [
                'required' => false,
                'type' => TextType::class,
                'label'    => $this->trans('Titre',  'Modules.Yathomecategories.Admin'),
            ])
            ->add('active', SwitchType::class, [
                'label'   => $this->trans('Active',  'Modules.Yathomecategories.Admin'),
                'required' => false,
            ])
            ->add('cover_image', FileType::class, [
                'label'    => $this->trans('Image',  'Modules.Yathomecategories.Admin'),
                'required' => false,
            ])
        ;
        
        $id = isset($options['data']['itemId']) ? $options['data']['itemId'] : null;
        $imagestimes = isset($options['data']['imagestimes']) ? $options['data']['imagestimes'] : null;
        $image_name = $id.'_'.$imagestimes;
        
        if ($id && file_exists(_PS_MODULE_DIR_. YatCategoryImageUploader::IMAGE_PATH . $image_name.'.jpg')) {
            $builder
                ->add('image_file', CustomContentType::class, [
                    'required' => false,
                    'template' => '@Modules/yathomecategories/views/templates/admin/upload_image.html.twig',
                    'data' => [
                        'itemId' => $id,
                        'imageUrl' => _MODULE_DIR_ .YatCategoryImageUploader::IMAGE_PATH . $image_name.'.jpg',
                    ],
                ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'label' => $this->trans('Customization', 'Modules.Yathomecategories.Admin'),
            ])
        ;
    }
}