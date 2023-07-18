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

namespace Yateo\Yatpush\CommandHandler;


use Yateo\Yatpush\Command\BulkDisablePushCommand;
use Yateo\Yatpush\Exception\PushException;
use Doctrine\ORM\EntityManagerInterface; 
use Yateo\Yatpush\Repository\PushRepository;

/**
 * Class BulkDisablePushCommandHandler is responsible for enabling cms category pages.
 */
final class BulkDisablePushCommandHandler implements BulkDisablePushCommandHandlerInterface
{

    /**
     * @param PushRepository
     */
    private $repository;

    /** 
     * @var EntityManagerInterface 
     */ 
    private $entityManager;

    public function __construct(
        PushRepository $repository,
        EntityManagerInterface $entityManager
    )
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }


    /**
     * {@inheritdoc}
     *
     * @throws PushException
     */
    public function handle(BulkDisablePushCommand $command)
    {
        try {
            foreach ($command->getPushIds() as $pushId) {

                $entity = $this->repository->findOneBy([
                    'id' => $pushId->getValue()
                ]);

                if(!$entity) {
                  throw new PushException(sprintf('Push object with id "%s" has not been found for enabling status.', $pushId->getValue()));
                }
                
                $entity->setActive(false);
                $this->entityManager->merge($entity);
                $this->entityManager->flush();

            }
        } catch (PushException $e) {
            throw new PushException('Unexpected error occurred when handling bulk Disable push', 0, $e);
        }
    }
}
