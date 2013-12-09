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

namespace Bangpound\Bundle\DoctrineCouchDBAdminBundle\Tests\Filter;

use Bangpound\Bundle\DoctrineCouchDBAdminBundle\Filter\CallbackFilter;
use Bangpound\Bundle\DoctrineCouchDBAdminBundle\Datagrid\ProxyQuery;

class CallbackFilterTest extends FilterWithQueryBuilderTest
{
    public function testFilterClosure()
    {
        $builder = new ProxyQuery($this->getQueryBuilder());

        $filter = new CallbackFilter;
        $filter->initialize('field_name', array(
            'callback' => function ($builder, $alias, $field, $value) {
                return true;
            }
        ));

        $filter->filter($builder, 'alias', 'field', 'myValue');

        $this->assertEquals(true, $filter->isActive());
    }

    public function testFilterMethod()
    {
        $builder = new ProxyQuery($this->getQueryBuilder());

        $filter = new CallbackFilter;
        $filter->initialize('field_name', array(
            'callback' => array($this, 'customCallback')
        ));

        $filter->filter($builder, 'alias', 'field', 'myValue');

        $this->assertEquals(true, $filter->isActive());
    }

    public function customCallback($builder, $alias, $field, $value)
    {
        return true;
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFilterException()
    {
        $builder = new ProxyQuery($this->getQueryBuilder());

        $filter = new CallbackFilter;
        $filter->initialize('field_name', array());

        $filter->filter($builder, 'alias', 'field', 'myValue');
    }
}
