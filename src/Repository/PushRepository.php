<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

declare(strict_types=1);

namespace Yateo\Yatpush\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Driver\Statement;
use Yateo\Yatpush\Exception\DatabaseException;
use Yateo\Yatpush\Uploader\YatpushImageUploader;

/**
 * Class PushRepository
 * @package Yateo\Yatpush\Repository
 */
class PushRepository extends EntityRepository
{

    /**
     * @param string $type
     * @param array $positionsData
     *
     * @return void
     */
    public function updatePositions(string $type, array $positionsData = []): void
    {
        try {
            $this->_em->getConnection()->beginTransaction();

            $i = 0;
            foreach ($positionsData['positions'] as $position) {
                $qb = $this->_em->getConnection()->createQueryBuilder();
                $qb
                    ->update(_DB_PREFIX_ . 'yateo_push')
                    ->set('position', ':position')
                    ->andWhere('id_yateo_push = :yateoPushId')
                    ->andWhere('type = :type')
                    ->setParameter('type', $type)
                    ->setParameter('yateoPushId', $position['rowId'])
                    ->setParameter('position', $i);

                ++$i;

                $statement = $qb->execute();
                if ($statement instanceof Statement && $statement->errorCode()) {
                    throw new DatabaseException('Could not update #%i');
                }
            }
            $this->_em->getConnection()->commit();
        } catch (DatabaseException $e) {
            $this->_em->getConnection()->rollBack();

            throw new DatabaseException('Could not update positions.');
        }
    }

    /**
     * @param string $type
     * @param int $id_lang
     * @return []
     */
    public function getItems(string $type, int $id_lang)
    {
        // $items = $this->findBy([
        //     'type' => $type,
        // ], [
        //     'position' => "ASC"
        // ]);

        $qb = $this->createQueryBuilder('a')
            ->select('a.id, a.type, a.active, a.position, b.link, b.textTop, b.textBottom, b.textButton')
            ->join('a.pushLangs', 'b')
            ->where('a.type = :type')
            ->setParameter('type', $type)
            ->andWhere('b.lang = :lang')
            ->setParameter('lang', $id_lang)
            ->orderBy('a.position', 'ASC')
        ;
        
        $image_dir = _PS_MODULE_DIR_.YatpushImageUploader::IMAGE_PATH;
        $image_path = _MODULE_DIR_.YatpushImageUploader::IMAGE_PATH;

        $items = array_map(function($a)use($image_path,$image_dir){
            $a['image'] = file_exists($image_dir.$a['id'].'.jpg') ?
                $image_path.$a['id'].'.jpg' :
                null
            ;
            return $a;
        }, $qb->getQuery()->getResult());

        return $items;
    }
}