<?php 

declare(strict_types=1);

namespace Yateo\Yathomecategories\CommandHandler;

use Doctrine\ORM\EntityManagerInterface;
use Yateo\Yathomecategories\Command\ToggleCategoryCommand;
use Yateo\Yathomecategories\Exception\CannotDeleteCategoryException;
use Yateo\Yathomecategories\Repository\CategoryRepository;

/**
 * Create new push
 */
final class ToggleCategoryCommandHandler
{
    /**
     * @param CategoryRepository
     */
    private $repository;

    /** 
     * @var EntityManagerInterface 
     */ 
    private $entityManager;

    public function __construct(
        CategoryRepository $repository,
        EntityManagerInterface $entityManager
    )
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function handle(ToggleCategoryCommand $command)
    {
        try {
            $entity = $this->repository->findOneBy([
                'id' => $command->getItemId()->getValue()
            ]);
            $entity->setActive($command->getActive());
            $this->entityManager->merge($entity);
            $this->entityManager->flush();
            return $command->getItemId();
        } catch(\PrestaShopException $e) {
            throw new CannotDeleteCategoryException('An error occured durring delete push');
        }
        
    }
    
}