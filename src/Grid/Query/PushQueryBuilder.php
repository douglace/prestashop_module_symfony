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

namespace Yateo\Yatpush\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\DoctrineFilterApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\SqlFilters;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Defines all required sql statements to render products list.
 */
class PushQueryBuilder  extends AbstractDoctrineQueryBuilder
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
            ->select('yp.`id_yateo_push`, yp.`position`, yp.`active`, yp.`type`')
            ->addSelect('ypl.`id_lang`,ypl.`link`, ypl.`text_top`, ypl.`text_bottom` , ypl.`text_button`')
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
        $qb->select('COUNT(yp.`id_yateo_push`)');

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
            ->from($this->dbPrefix . 'yateo_push', 'yp')
            ->leftJoin(
                'yp',
                $this->dbPrefix . 'yateo_push_lang',
                'ypl',
                'ypl.`id_yateo_push` = yp.`id_yateo_push` AND ypl.`id_lang` = :id_lang'
            )
            ->andWhere('yp.`type`="'.$this->type.'"')
        ;

        $sqlFilters = new SqlFilters();
        $sqlFilters
            ->addFilter(
                'id_yateo_push',
                'yp.`id_yateo_push`',
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

            if ('text_top' === $filterName) {
                $qb->andWhere('ypl.`text_top` LIKE :text_top');
                $qb->setParameter('text_top', '%' . $filter . '%');

                continue;
            }

            if ('text_bottom' === $filterName) {
                $qb->andWhere('ypl.`text_bottom` LIKE :text_bottom');
                $qb->setParameter('text_bottom', '%' . $filter . '%');

                continue;
            }

            if ('text_button' === $filterName) {
                $qb->andWhere('ypl.`text_button` LIKE :text_button');
                $qb->setParameter('text_button', '%' . $filter . '%');

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