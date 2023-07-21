<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */
declare(strict_types=1);

namespace Yateo\Yathomecategories\Form\Provider;


use Yateo\Yathomecategories\Entity\YateoHomeCategories;
use Yateo\Yathomecategories\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

class CategoryFormDataProvider implements FormDataProviderInterface
{
/**
     * @var CategoryRepository
     */
    private $repository;

    /**
     * @param CategoryRepository $repository
     */
    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($itemId)
    {
        
        /**
         * @var YateoHomeCategories
         */
        $category = $this->repository->findOneById($itemId);
        
        $categoryData = [
            'active' => $category->getActive(),
            'position' => $category->getActive(),
            'type' => $category->getType(),
            'imagestimes' => $category->getImagetimes(),
        ];
        foreach ($category->getCategoryLangs() as $categoryLangs) {
            $categoryData['link'][$categoryLangs->getLang()->getId()] = $categoryLangs->getLink();
            $categoryData['title'][$categoryLangs->getLang()->getId()] = $categoryLangs->getTitle();
        }
        
        return $categoryData;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'type' => 'MAN'
        ];
    }
}