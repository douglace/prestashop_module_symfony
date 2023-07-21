<?php 

declare(strict_types=1);

namespace Yateo\Yathomecategories\CommandBuilder;

use Yateo\Yathomecategories\ValueObject\ItemId;

interface CategoryCommandBuilderInterface
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
    public function buildEditCommands(ItemId $itemId, array $data);
}