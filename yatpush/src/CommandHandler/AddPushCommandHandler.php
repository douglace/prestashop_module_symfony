<?php 

declare(strict_types=1);

namespace Yateo\Yatpush\CommandHandler;

use Yateo\Yatpush\Command\AddPushCommand;
use Yateo\Yatpush\Entity\YateoPush;
use Yateo\Yatpush\Entity\YateoPushLang;
use Yateo\Yatpush\ValueObject\PushId;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Yateo\Yatpush\Exception\CannotAddPushException;

/**
 * Create new push
 */
final class AddPushCommandHandler
{
    public function handle(AddPushCommand $command)
    {
        $entity = new YateoPush();
        $this->createPushFromCommand($entity, $command);
        return new PushId((int) $entity->getId());
    }

    private function get(string $service)
    {
        return SymfonyContainer::getInstance()->get($service);
    }

    private function createPushFromCommand(YateoPush $push, AddPushCommand $command)
    {
        try {
            $push->setActive((bool)$command->getActive());
            $push->setPosition((int)$command->getPosition());
            $push->setType((string)$command->getType());
            
            $langRepository = $this->get('prestashop.core.admin.lang.repository');
            $languages = $langRepository->findAll();
            // The entity manager will allow us to persist the Doctrine entities
            $entityManager = $this->get('doctrine.orm.default_entity_manager');

            foreach($languages as $language){
                $pushLang = new YateoPushLang();
                $pushLang->setLang($language);

                if(isset($command->getLink()[$language->getId()])){
                    $pushLang->setLink($command->getLink()[$language->getId()]);
                }

                if(isset($command->getTextBottom()[$language->getId()])){
                    $pushLang->setTextBottom($command->getTextBottom()[$language->getId()]);
                }

                if(isset($command->getTextTop()[$language->getId()])){
                    $pushLang->setTextTop($command->getTextTop()[$language->getId()]);
                }

                if(isset($command->getTextButton()[$language->getId()])){
                    $pushLang->setTextButton($command->getTextButton()[$language->getId()]);
                }
                $push->addPushLangs($pushLang);
            }
            $entityManager->persist($push);
            $entityManager->flush();
        } catch (\PrestaShopException $e) {
            throw new CannotAddPushException('An error occured durring save push');
        }
    }
}