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
     * Find item for current language
     * @param int $number
     * @param int $page
     * @return array
     */
    public function findForCurrentLanguage($number = -1, $page = 1)
    {
        // Build query base on number of needed items and page number
        $queryBuilder = $this->getQueryBuilderForCurrentLanguage($number, $page);

        // Call database and return result
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Find all items for current language
     * @return array
     */
    public function findAllForCurrentLanguage()
    {
        // Call findForCurrentLanguage with default parameters (no pagination limit)
        return $this->findForCurrentLanguage();
    }

    /**
     * Find X random item(s) for current language
     * @param int $number
     * @return array
     */
    public function findRandomForCurrentLanguage($number = -1)
    {
        // Get query base on number of needed items and language
        $queryBuilder = $this->getQueryBuilderForCurrentLanguage($number);

        // Order items by rand in the query
        $queryBuilder->orderBy('rand()');

        // Call database and return result
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Create query builder and include language request parameter for current language
     *
     * @param int $number Optional parameter to set the maximum number of results to retrieve
     * @param int $page Optional parameter to sets the position of the first result to retrieve
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBuilderForCurrentLanguage($number = -1, $page = 1)
    {
        // Get global language set by user
        global $defaultTranslationLanguage;

        // Build query to filter by language
        $queryBuilder = $this->createQueryBuilder('e');
        $queryBuilder
            ->join('e.translations', 'et')
            ->andWhere('et.language = :language')
            ->setParameter('language', $defaultTranslationLanguage)
        ;

        // Set max result if set
        if ($number !== -1) {
            $queryBuilder->setMaxResults($number);
        }

        // Handle pagination
        $queryBuilder->setFirstResult(($page - 1) * $number);

        // Return query builder
        return $queryBuilder;
    }
}
