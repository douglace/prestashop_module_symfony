<?php 

declare(strict_types=1);

namespace Yateo\Yathomecategories\CommandBuilder;

use Yateo\Yathomecategories\ValueObject\ItemId;
use Yateo\Yathomecategories\Command\AddCategoryCommand;
use Yateo\Yathomecategories\Command\EditCategoryCommand;
use Yateo\Yathomecategories\Exception\InvalidCategoryPropertyException;

class CategoryCommandBuilder implements CategoryCommandBuilderInterface
{
    public function buildCommands(array $data): AddCategoryCommand
    {
        $command = new AddCategoryCommand();
        $this->build($command, $data);
        return $command;
    }

    public function buildEditCommands(ItemId $itemId, array $data): EditCategoryCommand
    {
        $command = new EditCategoryCommand($itemId);
        $this->build($command, $data);
        
        return $command;
    }

    private function build($command , array $data)
    {
        if(isset($data['active']))
        {
            $command->setActive((bool) $data['active']);
        }

        if(isset($data['position']))
        {
            $command->setPosition((int) $data['position']);
        }

        if(isset($data['type']))
        {
            if(!in_array($data['type'], ['MAN', 'WOMAN']))
            {
                throw new InvalidCategoryPropertyException('Invalid type category');
            }
            $command->setType((string) $data['type']);
        }

        if(isset($data['link']))
        {
            $command->setLink((array) $data['link']);
        }

        if(isset($data['title']))
        {
            $command->setTitle((array) $data['title']);
        }

    }
}