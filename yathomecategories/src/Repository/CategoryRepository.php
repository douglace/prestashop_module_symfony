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

namespace Yateo\Yathomecategories\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Driver\Statement;
use Yateo\Yathomecategories\Exception\DatabaseException;
use Yateo\Yathomecategories\Uploader\YatCategoryImageUploader;

/**
 * Class CategoryRepository
 * @package Yateo\Yathomecategories\Repository
 */
class CategoryRepository extends EntityRepository
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
                    ->update(_DB_PREFIX_ . 'yateo_home_categories')
                    ->set('position', ':position')
                    ->andWhere('id_yateo_home_categories = :yateoItemId')
                    ->andWhere('type = :type')
                    ->setParameter('type', $type)
                    ->setParameter('yateoItemId', $position['rowId'])
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
    public function getItems(string $type, int $id_lang, $active = true)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.id, a.type, a.active, a.position, b.link, b.title, a.imagetimes')
            ->join('a.categoryLangs', 'b')
            ->where('a.type = :type')
            ->setParameter('type', $type)
            ->andWhere('a.active = :active')
            ->andWhere('b.lang = :lang')
            ->setParameter('lang', $id_lang)
            ->setParameter('active', $active)
            ->orderBy('a.position', 'ASC')
        ;
        
        $image_dir = _PS_MODULE_DIR_.YatCategoryImageUploader::IMAGE_PATH;
        $image_path = _MODULE_DIR_.YatCategoryImageUploader::IMAGE_PATH;

        $items = array_map(function($a)use($image_path,$image_dir){
            $a['image'] = file_exists($image_dir.$a['id'].'_'.$a['imagetimes'].'.jpg') ?
                $image_path.$a['id'].'_'.$a['imagetimes'].'.jpg' :
                null
            ;
            return $a;
        }, $qb->getQuery()->getResult());

        return $items;
    }
}