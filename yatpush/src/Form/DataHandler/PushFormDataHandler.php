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

namespace Yateo\Yatpush\Form\DataHandler;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Yateo\Yatpush\CommandBuilder\PushBuilderInterface;
use Yateo\Yatpush\Uploader\YatpushImageUploader;
use Yateo\Yatpush\ValueObject\PushId;

/**
 * Class ContactFormDataHandler is responsible for handling create and update of contact form.
 */
final class PushFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var PushBuilderInterface
     */
    private $builder;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(
        CommandBusInterface $commandBus,
        PushBuilderInterface $builder
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
        $pushId = $this->commandBus->handle($command);
        $this->uploadImage($pushId, $data);
        return $pushId;
    }

    /**
     * {@inheritdoc}
     *
     * @throws DomainException
     */
    public function update($pushId, array $data)
    {
        $command = $this->builder->buildEditCommands(new PushId($pushId), $data);
        $pushId = $this->commandBus->handle($command);
        $this->uploadImage($pushId, $data);
        
        return $pushId;
    }

    private function uploadImage($pushId, array $data)
    {
        $image = isset($data['cover_image']) && $data['cover_image'] 
            ? $data['cover_image']
            : null 
        ;
       
        if($image == null || !($image instanceof UploadedFile)) {
            return false;
        }
        
        $uploader = new YatpushImageUploader();
        $uploader->upload($pushId->getValue(), $image);
    }
}