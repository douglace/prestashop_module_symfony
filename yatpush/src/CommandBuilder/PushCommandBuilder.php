<?php 

declare(strict_types=1);

namespace Yateo\Yatpush\CommandBuilder;

use Yateo\Yatpush\ValueObject\PushId;
use Yateo\Yatpush\Command\AddPushCommand;
use Yateo\Yatpush\Command\EditPushCommand;
use Yateo\Yatpush\Exception\PushException;

class PushCommandBuilder implements PushBuilderInterface
{
    public function buildCommands(array $data): AddPushCommand
    {
        $command = new AddPushCommand();
        $this->build($command, $data);
        return $command;
    }

    public function buildEditCommands(PushId $pushId, array $data): EditPushCommand
    {
        $command = new EditPushCommand($pushId);
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
                throw new PushException('Invalid type push');
            }
            $command->setType((string) $data['type']);
        }

        if(isset($data['link']))
        {
            $command->setLink((array) $data['link']);
        }

        if(isset($data['text_top']))
        {
            $command->setTextTop((array) $data['text_top']);
        }

        if(isset($data['text_bottom']))
        {
            $command->setTextBottom((array) $data['text_bottom']);
        }

        if(isset($data['text_button']))
        {
            $command->setTextButton((array) $data['text_button']);
        }
    }
}