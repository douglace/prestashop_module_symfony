<?php 

declare(strict_types=1);

namespace Yateo\Yatpush\CommandHandler;

use Doctrine\ORM\EntityManagerInterface;
use Yateo\Yatpush\Command\TogglePushCommand;
use Yateo\Yatpush\Exception\CannotDeletePushException;
use Yateo\Yatpush\Repository\PushRepository;

/**
 * Create new push
 */
final class TogglePushCommandHandler
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

    public function handle(TogglePushCommand $command)
    {
        try {
            $entity = $this->repository->findOneBy([
                'id' => $command->getPushId()->getValue()
            ]);
            $entity->setActive($command->getActive());
            $this->entityManager->merge($entity);
            $this->entityManager->flush();
            return $command->getPushId();
        } catch(\PrestaShopException $e) {
            throw new CannotDeletePushException('An error occured durring delete push');
        }
        
    }
    
}