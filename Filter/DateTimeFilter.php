<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bangpound\Bundle\DoctrineCouchDBAdminBundle\Filter;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

class DateTimeFilter extends AbstractDateFilter
{
    /**
     * Flag indicating that filter will filter by datetime instead by date
     * @var boolean
     */
    protected $time = true;

    /**
     * @param \Sonata\AdminBundle\Datagrid\ProxyQueryInterface $query
     * @param string                                           $field
     * @param array                                            $data
     */
    protected function applyTypeIsLessEqual(ProxyQueryInterface $query, $field, $data)
    {
        // Add a minute so less then equal selects all seconds.
        $data['value']->add(new \DateInterval('PT1M'));

        $this->applyType($query, $this->getOperator($data['type']), $field, $data['value']);
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ProxyQueryInterface $query
     * @param string                                           $field
     * @param array                                            $data
     */
    protected function applyTypeIsGreaterThan(ProxyQueryInterface $query, $field, $data)
    {
        // Add 59 seconds so anything above the minute is selected
        $data['value']->add(new \DateInterval('PT59S'));

        $this->applyType($query, $this->getOperator($data['type']), $field, $data['value']);
    }

    /**
     * Because we lack a second variable we select a range covering the entire minute.
     *
     * @param \Sonata\AdminBundle\Datagrid\ProxyQueryInterface $query
     * @param string                                           $field
     * @param array                                            $data
     */
    protected function applyTypeIsEqual(ProxyQueryInterface $query, $field, $data)
    {
        /** @var \DateTime $end */
        /** @var \Doctrine\CouchDB\View\Query $query */
        $end = clone $data['value'];
        $end->add(new \DateInterval('PT1M'));

        $query->setStartKey([
            $query->getDocumentType(),
            $field,
            $data['value']->format('Y-m-d H:i:s.u'),
        ]);

        $query->setEndKey([
            $query->getDocumentType(),
            $field,
            $end->format('Y-m-d H:i:s.u'),
        ]);
    }
}
