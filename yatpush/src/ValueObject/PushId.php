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

namespace Yateo\Yatpush\ValueObject;

use Yateo\Yatpush\Exception\PushException;

final class PushId
{
    /**
     * @var int
     */
    private $pushId;

    /**
     * @param int $pushId
     *
     * @throws ShopException
     */
    public function __construct(int $pushId)
    {
        $this->assertIsGreaterThanZero($pushId);

        $this->pushId = $pushId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->pushId;
    }

    /**
     * @param int $pushId
     *
     * @throws ShopException
     */
    private function assertIsGreaterThanZero(int $pushId): void
    {
        if (0 >= $pushId) {
            throw new PushException(
                sprintf(
                    'Push id %s is invalid. Push id must be number that is greater than zero.',
                    var_export($pushId, true)
                )
            );
        }
    }
}