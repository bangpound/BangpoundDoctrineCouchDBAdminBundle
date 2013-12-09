<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bangpound\Bundle\DoctrineCouchDBAdminBundle;

use Doctrine\ODM\CouchDB\View\ODMQuery;
use Exporter\Exception\InvalidMethodCallException;
use Doctrine\CouchDB\View\Query;
use Doctrine\CouchDB\View\Result;
use Exporter\Source\SourceIteratorInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;

class DoctrineODMQuerySourceIterator implements SourceIteratorInterface
{
    /**
     * @var \Doctrine\CouchDB\View\Query
     */
    protected $query;

    /**
     * @var \ArrayIterator
     */
    protected $iterator;

    protected $propertyPaths;

    /**
     * @var PropertyAccess
     */
    protected $propertyAccessor;

    /**
     * @var string default DateTime format
     */
    protected $dateTimeFormat;

    /**
     * @param ProxyQueryInterface $query The Doctrine Query
     * @param array $fields Fields to export
     * @param string $dateTimeFormat
     */
    public function __construct(ProxyQueryInterface $query, array $fields, $dateTimeFormat = '')
    {
        $this->query = clone $query;

        // Note : will be deprecated in Symfony 3.0, conserved for 2.2 compatibility
        // Use createPropertyAccessor() for 3.0
        // @see Symfony\Component\PropertyAccess\PropertyAccess
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();

        $this->propertyPaths = array();
        foreach ($fields as $name => $field) {
            if (is_string($name) && is_string($field)) {
                $this->propertyPaths[$name] = new PropertyPath($field);
            } else {
                $this->propertyPaths[$field] = new PropertyPath($field);
            }
        }

        $this->dateTimeFormat = $dateTimeFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $current = $this->iterator->current();

        $data = array();

        foreach ($this->propertyPaths as $name => $propertyPath) {
            $data[$name] = $this->getValue($this->propertyAccessor->getValue($current, $propertyPath));
        }

        //$this->query->getDocumentManager()->getUnitOfWork()->detach($current);
        return $data;
    }

    /**
     * @param $value
     *
     * @return null|string
     */
    protected function getValue($value)
    {
        if (is_array($value) or $value instanceof \Traversable) {
            $value = null;
        } elseif ($value instanceof \DateTime) {
            $value = $value->format($this->dateTimeFormat);
        } elseif (is_object($value)) {
            $value = (string) $value;
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        if ($this->iterator) {
            throw new InvalidMethodCallException('Cannot rewind a Doctrine\ODM\Query');
        }

        $result = $this->query->execute();
        $this->iterator = $result->getIterator();
        $this->iterator->rewind();
    }

    /**
     * @param string $dateTimeFormat
     */
    public function setDateTimeFormat($dateTimeFormat)
    {
        $this->dateTimeFormat = $dateTimeFormat;
    }

    /**
     * @return string
     */
    public function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }
}