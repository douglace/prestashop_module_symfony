<?php 

declare(strict_types=1);

namespace Yateo\Yathomecategories\CommandHandler;

use Yateo\Yathomecategories\Command\AddCategoryCommand;
use Yateo\Yathomecategories\Entity\YateoHomeCategories;
use Yateo\Yathomecategories\Entity\YateoHomeCategoriesLang;
use Yateo\Yathomecategories\ValueObject\ItemId;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Yateo\Yathomecategories\Exception\CannotAddCategoryException;

/**
 * Create new category
 */
final class AddCategoryCommandHandler
{
    public function handle(AddCategoryCommand $command)
    {
        $entity = new YateoHomeCategories();
        $this->createCategoryFromCommand($entity, $command);
        return new ItemId((int) $entity->getId());
    }

    private function get(string $service)
    {
        return SymfonyContainer::getInstance()->get($service);
    }

    private function createCategoryFromCommand(YateoHomeCategories $category, AddCategoryCommand $command)
    {
        try {
            $category->setActive((bool)$command->getActive());
            $category->setPosition((int)$command->getPosition());
            $category->setType((string)$command->getType());
            $category->setDateUpd(new \DateTime());

            $langRepository = $this->get('prestashop.core.admin.lang.repository');
            $languages = $langRepository->findAll();
            // The entity manager will allow us to persist the Doctrine entities
            $entityManager = $this->get('doctrine.orm.default_entity_manager');

            foreach($languages as $language){
                $categoryLang = new YateoHomeCategoriesLang();
                $categoryLang->setLang($language);

                if(isset($command->getLink()[$language->getId()])){
                    $categoryLang->setLink($command->getLink()[$language->getId()]);
                }

                if(isset($command->getTitle()[$language->getId()])){
                    $categoryLang->setTitle($command->getTitle()[$language->getId()]);
                }

                $category->addCategoryLangs($categoryLang);
            }
            $entityManager->persist($category);
            $entityManager->flush();
        } catch (\PrestaShopException $e) {
            throw new CannotAddCategoryException('An error occured durring save category');
        }
    }
}