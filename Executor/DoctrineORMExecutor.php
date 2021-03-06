<?php

namespace Zenstruck\DataGridBundle\Executor;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Zenstruck\DataGridBundle\Field\FieldCollection;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class DoctrineORMExecutor implements ExecutorInterface
{
    /** @var string */
    protected $dqlAlias;

    /** @var QueryBuilder */
    protected $qb;

    /**
     * @param string        $entity The Entity Class
     * @param EntityManager $em
     */
    public function __construct($entity, EntityManager $em)
    {
        $this->dqlAlias = 'e';
        $this->qb = $em->getRepository($entity)->createQueryBuilder($this->dqlAlias);
    }

    public function execute(FieldCollection $fieldCollection)
    {
        return $this
            ->filterQuery($fieldCollection)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param FieldCollection $fieldCollection
     *
     * @return QueryBuilder
     *
     * @throws \RuntimeException
     */
    public function filterQuery(FieldCollection $fieldCollection)
    {
        if (!$this->dqlAlias) {
            throw new \RuntimeException('The DQL Alias is not set.');
        }

        if (!$this->qb) {
            throw new \RuntimeException('The QueryBuidler is not set.');
        }

        foreach ($fieldCollection->all() as $field) {
            // do filters
            if ($value = $field->getFilterValue()) {
                $fieldName = $field->getName();
                $method = sprintf('filter%s', ucfirst(Inflector::classify($fieldName)));

                if (method_exists($this, $method)) {
                    // call custom method
                    $this->$method($value, $field);
                } else {
                    $paramName = sprintf(':%s_field', $fieldName);
                    $this->qb->andWhere(sprintf('%s.%s = %s', $this->dqlAlias, $fieldName, $paramName))
                        ->setParameter($paramName, $value);
                    ;
                }
            }

            // do sort
            if (($order = $field->getSortDirection()) && $field->isSortable()) {
                $this->qb->addOrderBy(sprintf('%s.%s', $this->dqlAlias, $field->getName()), strtoupper($order));
            }
        }

        // do search
        if ($query = $fieldCollection->getSearchQuery()) {
            $method = 'filterSearchQuery';

            if (method_exists($this, $method)) {
                // call custom method
                $this->$method($query, $fieldCollection);
            } else {
                // replace wildcard "*" with "%"
                $query = str_replace('*', '%', $query);

                if (!preg_match('/%/', $query)) {
                    // add wildcards to either side if none are set by user
                    $query = '%'.$query.'%';
                }

                $expressions = array();

                foreach ($fieldCollection->getSearchable() as $field) {
                    $expressions[] = $this->qb->expr()->like(sprintf('%s.%s', $this->dqlAlias, $field->getName()), ':search_query');
                }

                if (count($expressions)) {
                    $whereExpr = call_user_func_array(array($this->qb->expr(), 'orX'), $expressions);

                    $this->qb
                        ->andWhere($whereExpr)
                        ->setParameter('search_query', $query)
                    ;
                }
            }
        }

        return $this->qb;
    }
}
