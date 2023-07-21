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

namespace Yateo\Yathomecategories\ValueObject;

use Yateo\Yathomecategories\Exception\InvalidCategoryIdException;

final class ItemId
{
    /**
     * @var int
     */
    private $itemId;

    /**
     * @param int $itemId
     *
     * @throws ShopException
     */
    public function __construct(int $itemId)
    {
        $this->assertIsGreaterThanZero($itemId);

        $this->itemId = $itemId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->itemId;
    }

    /**
     * @param int $itemId
     *
     * @throws ShopException
     */
    private function assertIsGreaterThanZero(int $itemId): void
    {
        if (0 >= $itemId) {
            throw new InvalidCategoryIdException(
                sprintf(
                    'Category id %s is invalid. Categories id must be number that is greater than zero.',
                    var_export($itemId, true)
                )
            );
        }
    }
}