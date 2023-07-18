<?php 

declare(strict_types=1);

namespace Yateo\Yatpush\CommandBuilder;

use Yateo\Yatpush\ValueObject\PushId;

interface PushBuilderInterface
{
    /**
     * Create new commad
     * @param array $data
     */
    public function buildCommands(array $data);

    /**
     * Create new commad
     * @param array $data
     */
    public function buildEditCommands(PushId $pushId, array $data);
}