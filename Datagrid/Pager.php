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

use Sonata\AdminBundle\Datagrid\Pager as BasePager;
use Doctrine\ODM\CouchDB\View\Query;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

/**
 * Doctrine pager class.
 *
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @author     Kévin Dunglas <dunglas@gmail.com>
 */
class Pager extends BasePager
{
    /** @var ProxyQueryInterface */
    protected $query;

    /**
     * {@inheritdoc}
     */
    public function computeNbResult()
    {
        $countQuery = clone $this->query;
        $countQuery->setLimit(0);
        $result = $countQuery->execute();

        return $result->getTotalRows();
    }

    /**
     * {@inheritdoc}
     */
    public function getResults()
    {
        return $this->query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->resetIterator();

        $this->setNbResults($this->computeNbResult());

        $this->query->setFirstResult(null);
        $this->query->setMaxResults(null);

        if (count($this->getParameters()) > 0) {
            $this->query->setParameters($this->getParameters());
        }

        if (0 == $this->getPage() || 0 == $this->getMaxPerPage() || 0 == $this->getNbResults()) {
            $this->setLastPage(0);
        } else {
            $offset = ($this->getPage() - 1) * $this->getMaxPerPage();

            $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

            $this->query->setFirstResult($offset);
            $this->query->setMaxResults($this->getMaxPerPage());
        }
    }
}
