<?php 

declare(strict_types=1);

namespace Yateo\Yathomecategories\CommandHandler;

use DateTime;
use Yateo\Yathomecategories\Command\EditCategoryCommand;
use Yateo\Yathomecategories\Entity\YateoHomeCategories;
use Yateo\Yathomecategories\Entity\YateoHomeCategoriesLang;
use Yateo\Yathomecategories\ValueObject\ItemId;
use Doctrine\ORM\EntityManagerInterface; 
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Yateo\Yathomecategories\Exception\CannotAddCategoryException;
use Yateo\Yathomecategories\Repository\CategoryRepository;

/**
 * Create new category
 */
final class EditCategoryCommandHandler
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

    public function handle(EditCategoryCommand $command)
    {
        $entity = $this->repository->findOneBy([
            'id' => $command->getItemId()->getValue()
        ]);
        
        $this->updatePushFromCommand($entity, $command);
        return new ItemId((int) $entity->getId());
    }

    private function get(string $service)
    {
        return SymfonyContainer::getInstance()->get($service);
    }

    private function updatePushFromCommand(YateoHomeCategories $category, EditCategoryCommand $command)
    {
        try {
            $category->setActive((bool)$command->getActive());
            $category->setPosition((int)$command->getPosition());
            $category->setType((string)$command->getType());
            $category->setDateUpd(new DateTime());
            
            $langRepository = $this->get('prestashop.core.admin.lang.repository');
            $languages = $langRepository->findAll();

            foreach($languages as $language){
                $categoryLang = null;
                foreach($category->getCategoryLangs() as $pl){
                    if($pl->getLang()->getId() == $language->getId()) {
                        $categoryLang = $pl;
                        break;
                    }
                }
                
                if($categoryLang === null)
                {
                    $categoryLang = new YateoHomeCategoriesLang();
                    $categoryLang->setLang($language);
                }

                if(isset($command->getLink()[$language->getId()])){
                    $categoryLang->setLink($command->getLink()[$language->getId()]);
                }

                if(isset($command->getTitle()[$language->getId()])){
                    $categoryLang->setTitle($command->getTitle()[$language->getId()]);
                }

                $category->addCategoryLangs($categoryLang);
            }
            $this->entityManager->merge($category);
            $this->entityManager->flush();
        } catch(\PrestaShopException $e) {
            throw new CannotAddCategoryException('An error occured durring save category');
        }
    }
}