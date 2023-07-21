<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);

namespace Yateo\Yathomecategories\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\DoctrineFilterApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\SqlFilters;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Defines all required sql statements to render products list.
 */
class CategoryQueryBuilder  extends AbstractDoctrineQueryBuilder
{
    private $type = "MAN";

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int
     */
    private $contextLanguageId;


    /**
     * @var DoctrineFilterApplicatorInterface
     */
    private $filterApplicator;


    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        int $contextLanguageId,
        DoctrineFilterApplicatorInterface $filterApplicator,
        string $type
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextLanguageId = $contextLanguageId;
        $this->filterApplicator = $filterApplicator;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb
            ->select('yp.`id_yateo_home_categories`, yp.`position`, yp.`active`, yp.`type`, yp.`imagetimes`')
            ->addSelect('ypl.`id_lang`, ypl.`title`, ypl.`link`')
        ;

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;
        
        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(yp.`id_yateo_home_categories`)');

        return $qb;
    }

    /**
     * Gets query builder.
     *
     * @param array $filterValues
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filterValues): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'yateo_home_categories', 'yp')
            ->leftJoin(
                'yp',
                $this->dbPrefix . 'yateo_home_categories_lang',
                'ypl',
                'ypl.`id_yateo_home_categories` = yp.`id_yateo_home_categories` AND ypl.`id_lang` = :id_lang'
            )
            ->andWhere('yp.`type`="'.$this->type.'"')
        ;

        $sqlFilters = new SqlFilters();
        $sqlFilters
            ->addFilter(
                'id_yateo_home_categories',
                'yp.`id_yateo_home_categories`',
                SqlFilters::WHERE_STRICT
            );
        
        $this->filterApplicator->apply($qb, $sqlFilters, $filterValues);

        $qb->setParameter('id_lang', $this->contextLanguageId);
        
        
        foreach ($filterValues as $filterName => $filter) {
            if ('active' === $filterName) {
                $qb->andWhere('yp.`active` = :active');
                $qb->setParameter('active', $filter);

                continue;
            }

            if ('title' === $filterName) {
                $qb->andWhere('ypl.`title` LIKE :title');
                $qb->setParameter('title', '%' . $filter . '%');

                continue;
            }

            

            if ('position' === $filterName) {
                $qb->andWhere('yp.`position` LIKE :position');
                $qb->setParameter('position', '%' . $filter . '%');

                continue;
            }

        }

        return $qb;
    }

}