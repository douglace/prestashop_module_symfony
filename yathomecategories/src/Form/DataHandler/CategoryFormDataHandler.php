<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Yateo\Yathomecategories\Form\DataHandler;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Yateo\Yathomecategories\Command\EditImageTimesCommand;
use Yateo\Yathomecategories\CommandBuilder\CategoryCommandBuilderInterface;
use Yateo\Yathomecategories\Uploader\YatCategoryImageUploader;
use Yateo\Yathomecategories\ValueObject\ItemId;

/**
 * Class ContactFormDataHandler is responsible for handling create and update of contact form.
 */
final class CategoryFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var CategoryCommandBuilderInterface
     */
    private $builder;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(
        CommandBusInterface $commandBus,
        CategoryCommandBuilderInterface $builder
    )
    {
        $this->commandBus = $commandBus;
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     *
     * @throws DomainException
     */
    public function create(array $data)
    {
        $command = $this->builder->buildCommands($data);
        $itemId = $this->commandBus->handle($command);
        $this->uploadImage($itemId, $data);
        return $itemId;
    }

    /**
     * {@inheritdoc}
     *
     * @throws DomainException
     */
    public function update($itemId, array $data)
    {
        $command = $this->builder->buildEditCommands(new ItemId($itemId), $data);
        $itemId = $this->commandBus->handle($command);
        $this->uploadImage($itemId, $data);
        
        return $itemId;
    }

    private function uploadImage($itemId, array $data)
    {
        $image = isset($data['cover_image']) && $data['cover_image'] 
            ? $data['cover_image']
            : null 
        ;
       
        if($image == null || !($image instanceof UploadedFile)) {
            return false;
        }
        $imagetimes = (string)time();
        $uploader = new YatCategoryImageUploader();
      
        $uploader->setImagetimes($imagetimes);
        $uploader->setOldImagetimes($data['imagestimes']);
        
        $uploader->upload($itemId->getValue(), $image);

        $command = new EditImageTimesCommand(
            $itemId->getValue(),
            $imagetimes
        );
        $this->commandBus->handle($command);
    }
}