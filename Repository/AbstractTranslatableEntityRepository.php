<?php

namespace EasyAdminTranslationsBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class AbstractEntityRepository
 * @package EasyAdminTranslationsBundle\Repository
 */
abstract class AbstractTranslatableEntityRepository extends EntityRepository
{
    public function findAll($languageIsoKey = null)
    {
        if (!empty($languageIsoKey)) {
            $language = $this->getEntityManager()->getRepository('EasyAdminTranslationsBundle:Language')->findOneByIsoKey($languageIsoKey);
            if (!empty($language)) {
                $queryBuilder = $this->createQueryBuilder('e');
                $queryBuilder
                    ->join('e.translations', 'et')
                    ->andWhere('et.language = :language')
                    ->setParameter('language', $language)
                ;
                return $queryBuilder->getQuery()->getResult();
            }
        }
        return parent::findAll();
    }
}
