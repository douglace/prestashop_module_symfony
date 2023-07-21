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

namespace Yateo\Yatpush\QueryHandler;

use Yateo\Yatpush\Exception\PushNotFoundException;
use Yateo\Yatpush\Query\GetPushIsEnabled;
use Yateo\Yatpush\Repository\PushRepository;

/**
 * @internal
 */
final class GetPushIsEnabledHandler implements GetPushIsEnabledHandlerInterface
{

    /**
     * @param PushRepository
     */
    private $repository;
    

    public function __construct(PushRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetPushIsEnabled $query)
    {
        $pushId = $query->getPushId()->getValue();
        $push = $this->repository->findOneBy([
            'id' => $pushId
        ]);

        if ($push->getId() !== $pushId) {
            throw new PushNotFoundException($query->getPushId(), sprintf('Push with id "%s" was not found.', $pushId));
        }

        return (bool) $push->getActive();
    }
}
