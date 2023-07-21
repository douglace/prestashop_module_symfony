<?php 

declare(strict_types=1);

namespace Yateo\Yatpush\CommandHandler;

use Yateo\Yatpush\Command\DeletePushCommand;
use Doctrine\ORM\EntityManagerInterface; 
use Yateo\Yatpush\Exception\CannotDeletePushException;
use Yateo\Yatpush\Repository\PushRepository;

/**
 * Create new push
 */
final class DeletePushCommandHandler
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

    public function handle(DeletePushCommand $command)
    {
        try {
            $entity = $this->repository->findOneBy([
                'id' => $command->getPushId()->getValue()
            ]);
            if($entity) {
                $this->entityManager->remove($entity);
                $this->entityManager->flush();
            }
            
            return true;
        } catch(\PrestaShopException $e) {
            throw new CannotDeletePushException('An error occured durring delete push');
        }
        
    }
    
}