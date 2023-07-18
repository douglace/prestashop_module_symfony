<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */
declare(strict_types=1);

namespace Yateo\Yatpush\Form\Provider;

use Yateo\Yatpush\Entity\YateoPush;
use Yateo\Yatpush\Entity\YateoPushLang;
use Yateo\Yatpush\Repository\PushRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

class PushFormDataProvider implements FormDataProviderInterface
{
/**
     * @var PushRepository
     */
    private $repository;

    /**
     * @param PushRepository $repository
     */
    public function __construct(PushRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($pushId)
    {
        $push = $this->repository->findOneById($pushId);

        $pushData = [
            'active' => $push->getActive(),
            'position' => $push->getActive(),
            'type' => $push->getType(),
        ];
        foreach ($push->getPushLangs() as $pushLang) {
            $pushData['link'][$pushLang->getLang()->getId()] = $pushLang->getLink();
            $pushData['text_top'][$pushLang->getLang()->getId()] = $pushLang->getTextTop();
            $pushData['text_button'][$pushLang->getLang()->getId()] = $pushLang->getTextButton();
            $pushData['text_bottom'][$pushLang->getLang()->getId()] = $pushLang->getTextBottom();
        }
        
        return $pushData;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'type' => 'MAN'
        ];
    }
}