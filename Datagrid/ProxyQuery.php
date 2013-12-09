<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bangpound\Bundle\DoctrineCouchDBAdminBundle\Datagrid;

use Doctrine\ODM\CouchDB\DocumentRepository;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

/**
 * This class try to unify the query usage with Doctrine
 */
class ProxyQuery implements ProxyQueryInterface
{

    protected $query;
    protected $repository;

    /**
     * @var array
     */
    protected $params = array();

    protected $sortBy;
    protected $sortOrder;
    protected $firstResult;
    protected $maxResults;

    protected $documentType;

    /**
     * @param DocumentRepository $repository
     */
    public function __construct(DocumentRepository $repository, $designDocumentName = 'doctrine_repositories', $viewName = 'equal_constraint')
    {
        $this->repository = $repository;

        $this->documentType = str_replace("\\", ".", $repository->getDocumentName());

        $this->query = $repository
            ->getDocumentManager()
            ->createQuery($designDocumentName, $viewName)
        ;
    }

    /**
     * @param  array $params
     * @param  null  $hydrationMode
     * @return mixed
     */
    public function execute(array $params = array(), $hydrationMode = null)
    {
        return $this->query->onlyDocs(true)->execute();
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->query, $name), $args);
    }

    /**
     * @param array $parentAssociationMappings
     * @param array $fieldMapping
     */
    public function setSortBy($parentAssociationMappings, $fieldMapping)
    {
        // No-op because CouchDB can only sort by key.
    }

    public function getSortBy()
    {
        // No-op because CouchDB can only sort by key.
    }

    /**
     * @param mixed $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
        if ('DESC' === $this->sortOrder) {
            $this->query->setDescending(true);
        }
    }

    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    public function getSingleScalarResult()
    {
        $query = $this->query;

        return $query->getSingleResult();
    }

    /**
     * @param int $firstResult
     */
    public function setFirstResult($firstResult)
    {
        $this->firstResult = $firstResult;
        $this->query->setSkip((int) $this->firstResult);
    }

    public function getFirstResult()
    {
        return $this->firstResult;
    }

    /**
     * @param int $maxResults
     */
    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;
        $this->query->setLimit((int) $this->maxResults);
    }

    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * @return mixed
     */
    public function getUniqueParameterId()
    {
        // TODO: Implement getUniqueParameterId() method.
    }

    /**
     * @param array $associationMappings
     *
     * @return mixed
     */
    public function entityJoin(array $associationMappings)
    {
        // TODO: Implement entityJoin() method.
    }

}
