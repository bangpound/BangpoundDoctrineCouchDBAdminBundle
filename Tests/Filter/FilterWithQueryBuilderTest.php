<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bangpound\Bundle\DoctrineCouchDBAdminBundle\Tests\Filter;

class FilterWithQueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $queryBuilder = null;

    public function setUp()
    {
        $this->queryBuilder = $this->getMockBuilder('Doctrine\ODM\CouchDB\Query\Builder')
                ->disableOriginalConstructor()
                ->getMock();
        $this->queryBuilder
                ->expects($this->any())
                ->method('field')
                ->will($this->returnSelf())
        ;
    }

    protected function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

}
