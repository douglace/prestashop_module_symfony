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

namespace Yateo\Yathomecategories\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
/**
 * Gets data for men grid
 */
abstract class AbstractGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $menDataFactory;

    /**
     * @var ImageProviderInterface
     */
    private $menLogoThumbnailProvider;

    /**
     * @param GridDataFactoryInterface $menDataFactory
     * @param ImageProviderInterface $menLogoThumbnailProvider
     */
    public function __construct(
        GridDataFactoryInterface $menDataFactory,
        ImageProviderInterface $menLogoThumbnailProvider
    ) {
        $this->menDataFactory = $menDataFactory;
        $this->menLogoThumbnailProvider = $menLogoThumbnailProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $menData = $this->menDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $menData->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $menData->getRecordsTotal(),
            $menData->getQuery()
        );
    }

    /**
     * @param array $mens
     *
     * @return array
     */
    private function applyModification(array $mens)
    {
        foreach ($mens as $i => $men) {
           
            $mens[$i]['logo'] = $this->menLogoThumbnailProvider->getPath(
                $men['id_yateo_home_categories'].'_'.$men['imagetimes']
            );
        }

        return $mens;
    }
}
