<?php

namespace EasyAdminTranslationsBundle\Repository;

use \Doctrine\ORM\EntityRepository as DoctrineRepository;
use EasyAdminTranslationsBundle\Entity\Language;

/**
 * LanguageRepository
 */
class LanguageRepository extends DoctrineRepository
{

    /**
     * Find one language like is key ("en_" = "en_US" for example)
     * @param string $isoKey
     * @return Language
     */
    public function findOneLanguageLikeIsoKey($isoKey) {

        // Build query
        $queryBuilder = $this->createQueryBuilder('l');
        $queryBuilder
            ->andWhere('l.isoKey LIKE :isoKey')
            ->setParameter('isoKey', '%' . $isoKey . '%')
            ->setMaxResults(1)
        ;

        // Return results
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

}
