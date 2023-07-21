<?php 

declare(strict_types=1);

namespace Yateo\Yathomecategories\CommandHandler;

use Doctrine\ORM\EntityManagerInterface;
use Yateo\Yathomecategories\Command\EditImageTimesCommand;
use Yateo\Yathomecategories\Exception\CannotEditCategoryException;
use Yateo\Yathomecategories\Repository\CategoryRepository;

/**
 * Create new push
 */
final class EditImageTimesCommandHandler
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

    public function handle(EditImageTimesCommand $command)
    {
        try {
            $entity = $this->repository->findOneBy([
                'id' => $command->getItemId()->getValue()
            ]);
            $entity->setImagetimes($command->getImagetimes());
            $this->entityManager->merge($entity);
            $this->entityManager->flush();
            return $command->getItemId();
        } catch(\PrestaShopException $e) {
            throw new CannotEditCategoryException('An error occured durring edit item');
        }
        
    }
    
}