<?php

namespace EasyAdminTranslationsBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class AbstractEntityRepository
 * @package EasyAdminTranslationsBundle\Repository
 */
abstract class AbstractTranslatableEntityRepository extends EntityRepository
{
    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findForCurrentLanguage($limit = null, $offset = null)
    {
        $queryBuilder = $this->getQueryBuilderForCurrentLanguage($limit, $offset);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int $limit
     * @return array
     */
    public function findRandomForCurrentLanguage($limit = null)
    {
        $queryBuilder = $this->getQueryBuilderForCurrentLanguage($limit);

        $queryBuilder->orderBy('rand()');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Create query builder and include language request parameter for current language
     *
     * @param int $limit Optional parameter to set the maximum number of results to retrieve
     * @param int $offset Optional parameter to sets the position of the first result to retrieve
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBuilderForCurrentLanguage($limit = null, $offset = null)
    {
        global $defaultTranslationLanguage;

        $queryBuilder = $this->createQueryBuilder('e');
        $queryBuilder
            ->join('e.translations', 'et')
            ->andWhere('et.language = :language')
            ->setParameter('language', $defaultTranslationLanguage)
        ;

        if ($limit !== null) {
            $queryBuilder->setMaxResults($limit);
        }

        if ($offset !== null) {
            $queryBuilder->setFirstResult($offset);
        }

        return $queryBuilder;
    }
}
