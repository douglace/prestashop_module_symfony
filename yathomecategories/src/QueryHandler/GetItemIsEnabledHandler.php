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

namespace Yateo\Yathomecategories\QueryHandler;

use Yateo\Yathomecategories\Exception\NotFoundCategoryException;
use Yateo\Yathomecategories\Query\GetItemIsEnabled;
use Yateo\Yathomecategories\Repository\CategoryRepository;

/**
 * @internal
 */
final class GetItemIsEnabledHandler implements GetItemIsEnabledHandlerInterface
{

    /**
     * @param CategoryRepository
     */
    private $repository;
    

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetItemIsEnabled $query)
    {
        $itemId = $query->getItemId()->getValue();
        $category = $this->repository->findOneBy([
            'id' => $itemId
        ]);

        if ($category->getId() !== $itemId) {
            throw new NotFoundCategoryException(sprintf('Categorie with id "%s" was not found.', $itemId));
        }

        return (bool) $category->getActive();
    }
}
