<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace Yateo\Yatpush\Exception;

use Yateo\Yatpush\ValueObject\PushId;

class PushNotFoundException extends PushException
{
    /**
     * @var PushId
     */
    private $pushId;

    /**
     * @param CategoryId $categoryId
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct(PushId $pushId, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->pushId = $pushId;
    }

    /**
     * @return PushId
     */
    public function getPushId()
    {
        return $this->pushId;
    }
}