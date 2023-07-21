<?php 

declare(strict_types=1);

namespace Yateo\Yatpush\CommandHandler;

use Yateo\Yatpush\Command\EditPushCommand;
use Yateo\Yatpush\Entity\YateoPush;
use Yateo\Yatpush\Entity\YateoPushLang;
use Yateo\Yatpush\ValueObject\PushId;
use Doctrine\ORM\EntityManagerInterface; 
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Yateo\Yatpush\Exception\CannotAddPushException;
use Yateo\Yatpush\Repository\PushRepository;

/**
 * Create new push
 */
final class EditPushCommandHandler
{
    /**
     * @param PushRepository
     */
    private $repository;

    /** 
     * @var EntityManagerInterface 
     */ 
    private $entityManager;

    public function __construct(
        PushRepository $repository,
        EntityManagerInterface $entityManager
    )
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function handle(EditPushCommand $command)
    {
        $entity = $this->repository->findOneBy([
            'id' => $command->getPushId()->getValue()
        ]);
        
        $this->updatePushFromCommand($entity, $command);
        return new PushId((int) $entity->getId());
    }

    private function get(string $service)
    {
        return SymfonyContainer::getInstance()->get($service);
    }

    private function updatePushFromCommand(YateoPush $push, EditPushCommand $command)
    {
        try {
            $push->setActive((bool)$command->getActive());
            $push->setPosition((int)$command->getPosition());
            $push->setType((string)$command->getType());
            
            $langRepository = $this->get('prestashop.core.admin.lang.repository');
            $languages = $langRepository->findAll();

            foreach($languages as $language){
                $pushLang = null;
                foreach($push->getPushLangs() as $pl){
                    if($pl->getLang()->getId() == $language->getId()) {
                        $pushLang = $pl;
                        break;
                    }
                }
                
                if($pushLang === null)
                {
                    $pushLang = new YateoPushLang();
                    $pushLang->setLang($language);
                }

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
            $this->entityManager->merge($push);
            $this->entityManager->flush();
        } catch(\PrestaShopException $e) {
            throw new CannotAddPushException('An error occured durring save push');
        }
    }
}