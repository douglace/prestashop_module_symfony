<?php 

declare(strict_types=1);

namespace Yateo\Yathomecategories\CommandHandler;

use Yateo\Yathomecategories\Command\DeleteCategoryCommand;
use Doctrine\ORM\EntityManagerInterface; 
use Yateo\Yathomecategories\Exception\CannotDeleteCategoryException;
use Yateo\Yathomecategories\Repository\CategoryRepository;

/**
 * Create new push
 */
final class DeleteCategoryCommandHandler
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

    public function handle(DeleteCategoryCommand $command)
    {
        try {
            $entity = $this->repository->findOneBy([
                'id' => $command->getItemId()->getValue()
            ]);
            if($entity) {
                $this->entityManager->remove($entity);
                $this->entityManager->flush();
            }
            
            return true;
        } catch(\PrestaShopException $e) {
            throw new CannotDeleteCategoryException('An error occured durring delete push');
        }
        
    }
    
}