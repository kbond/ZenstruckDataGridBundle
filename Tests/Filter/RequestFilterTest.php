<?php

namespace Zenstruck\DataGridBundle\Tests\Filter;

use Symfony\Component\HttpFoundation\Request;
use Zenstruck\DataGridBundle\Field\Field;
use Zenstruck\DataGridBundle\Field\FieldCollection;
use Zenstruck\DataGridBundle\Filter\RequestFilter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RequestFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterFields()
    {
        $filter = new RequestFilter(Request::create(sprintf('/foo?%s[foo]=baz', RequestFilter::PARAM_FILTER)));

        $fieldCollection = new FieldCollection(array(
            new Field('foo'),
            new Field('bar', array('label' => 'baz'))
        ));
        $results = $filter->filter($fieldCollection);
        $this->assertCount(2, $results);

        $fieldCollection = new FieldCollection(array(
            new Field('foo', array('filterable' => true)),
            new Field('bar', array('label' => 'baz'))
        ));
        $results = $filter->filter($fieldCollection);
        $this->assertCount(2, $results);
        $this->assertEquals('baz', $results->get('foo')->getFilterValue());
        $this->assertNull($results->get('bar')->getFilterValue());

        $fieldCollection = new FieldCollection(array(
            new Field('foo', array('filterable' => true)),
            new Field('bar', array('filterable' => true))
        ));
        $results = $filter->filter($fieldCollection);
        $this->assertCount(2, $results);
        $this->assertEquals('baz', $results->get('foo')->getFilterValue());
        $this->assertNull($results->get('bar')->getFilterValue());
    }

    public function testSortFields()
    {
        $filter = new RequestFilter(Request::create(sprintf('/foo?%s[foo]=DESC', RequestFilter::PARAM_SORT)));
        $fieldCollection = new FieldCollection(array(
            new Field('foo'),
            new Field('bar')
        ));
        $results = $filter->filter($fieldCollection);
        $this->assertEquals(Field::SORT_ASC, $results->get('foo')->getSortDirection());

        $filter = new RequestFilter(Request::create(sprintf('/foo?%s[foo]=DESC', RequestFilter::PARAM_SORT)));
        $fieldCollection = new FieldCollection(array(
            new Field('foo', array('sortable' => true)),
            new Field('bar')
        ));
        $results = $filter->filter($fieldCollection);
        $this->assertEquals(Field::SORT_DESC, $results->get('foo')->getSortDirection());

        // invalid sort
        $filter = new RequestFilter(Request::create(sprintf('/foo?%s[foo]=BAZ', RequestFilter::PARAM_SORT)));
        $fieldCollection = new FieldCollection(array(
            new Field('foo', array('sortable' => true)),
            new Field('bar')
        ));
        $results = $filter->filter($fieldCollection);
        $this->assertEquals(Field::SORT_ASC, $results->get('foo')->getSortDirection());
    }

    public function testNoQuery()
    {
        $filter = new RequestFilter(Request::create('/foo'));

        $fieldCollection = new FieldCollection(array(
            new Field('foo'),
            new Field('bar', array('label' => 'baz'))
        ));
        $results = $filter->filter($fieldCollection);
        $this->assertNull($results->get('foo')->getFilterValue());
        $this->assertNull($results->get('bar')->getFilterValue());
        $this->assertEquals(Field::SORT_ASC, $results->get('foo')->getSortDirection());
        $this->assertEquals(Field::SORT_ASC, $results->get('bar')->getSortDirection());

        $fieldCollection = new FieldCollection(array(
            new Field('foo', array('filterable' => true)),
            new Field('bar', array('filterable' => true))
        ));
        $results = $filter->filter($fieldCollection);
        $this->assertNull($results->get('foo')->getFilterValue());
        $this->assertNull($results->get('bar')->getFilterValue());
        $this->assertEquals(Field::SORT_ASC, $results->get('foo')->getSortDirection());
        $this->assertEquals(Field::SORT_ASC, $results->get('bar')->getSortDirection());
    }
}