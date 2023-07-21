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

namespace Yateo\Yatpush\Command;

use Yateo\Yatpush\Exception\PushException;
use Yateo\Yatpush\ValueObject\PushId;

/**
 * Class AbstractBulkPushCommand is responsible for providing shared logic between all bulk actions
 * in cms page category listing.
 */
abstract class AbstractBulkPushCommand
{
    /**
     * @var PushId[]
     */
    private $pushIds;

    /**
     * @param int[] $pushIds
     *
     * @throws PushException
     */
    public function __construct(array $pushIds)
    {
        if ($this->assertIsEmptyOrContainsNonIntegerValues($pushIds)) {
            throw new PushException(sprintf('Missing push data or array %s contains non integer values for bulk enabling'));
        }

        $this->setPushIds($pushIds);
    }

    /**
     * @return PushId[]
     */
    public function getPushIds()
    {
        return $this->pushIds;
    }

    /**
     * @param int[] $pushIds
     *
     * @throws PushException
     */
    private function setPushIds(array $pushIds)
    {
        foreach ($pushIds as $id) {
            $this->pushIds[] = new PushId($id);
        }
    }

    /**
     * @param array $ids
     *
     * @return bool
     */
    protected function assertIsEmptyOrContainsNonIntegerValues(array $ids)
    {
        return empty($ids) || $ids !== array_filter($ids, 'is_int');
    }
}
