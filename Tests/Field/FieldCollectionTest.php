<?php

namespace Zenstruck\DataGridBundle\Tests\Field;

use Zenstruck\DataGridBundle\Field\Field;
use Zenstruck\DataGridBundle\Field\FieldCollection;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FieldCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testFieldArrayConstructor()
    {
        $fieldCollection = new FieldCollection(array(
            new Field('foo'),
            new Field('bar', array('label' => 'baz'))
        ));

        $this->assertCount(2, $fieldCollection);
        $this->assertTrue($fieldCollection->has('foo'));
        $this->assertEquals('foo', $fieldCollection->get('foo')->getName());
        $this->assertEquals('bar', $fieldCollection->get('bar')->getName());
    }

    public function testConfigArrayConstructor()
    {
        $fieldCollection = new FieldCollection(array(
            'foo' => array(),
            'bar' => array(
                'label' => 'baz'
            )
        ));

        $this->assertCount(2, $fieldCollection);
        $this->assertTrue($fieldCollection->has('foo'));
        $this->assertEquals('foo', $fieldCollection->get('foo')->getName());
        $this->assertEquals('bar', $fieldCollection->get('bar')->getName());
    }

    public function testGetVisibleFields()
    {
        $fieldCollection = new FieldCollection(array(
            new Field('foo'),
            new Field('bar')
        ));
        $this->assertCount(2, $fieldCollection->getVisible());

        $fieldCollection = new FieldCollection(array(
            new Field('foo'),
            new Field('bar', array('visible' => false))
        ));
        $this->assertCount(1, $fieldCollection->getVisible());
    }

    public function testSetFilterValues()
    {
        $fieldCollection = new FieldCollection(array(
            'foo' => array(),
            'bar' => array()
        ));
        $results = $fieldCollection->setFilterValues(array('foo' => 'baz'));
        $this->assertEquals($fieldCollection, $results);

        $fieldCollection = new FieldCollection(array(
            'foo' => array('filterable' => true),
            'bar' => array()
        ));
        $results = $fieldCollection->setFilterValues(array('foo' => 'baz'));
        $this->assertEquals('baz', $results->get('foo')->getFilterValue());

        $fieldCollection = new FieldCollection(array(
            'foo' => array('filterable' => true),
            'bar' => array()
        ));
        $results = $fieldCollection->setFilterValues(array('foo' => 'baz'));
        $this->assertEquals('baz', $results->get('foo')->getFilterValue());

        $fieldCollection = new FieldCollection(array(
            'foo' => array('filterable' => true, 'filter_value' => 'baz'),
            'bar' => array()
        ));
        $results = $fieldCollection->setFilterValues(array());
        $this->assertEquals('baz', $results->get('foo')->getFilterValue());
    }

    public function testSetSortDirections()
    {
        $fieldCollection = new FieldCollection(array(
            'foo' => array(),
            'bar' => array()
        ));
        $results = $fieldCollection->setSortDirections(array('foo' => Field::SORT_DESC));
        $this->assertNull($results->get('foo')->getSortDirection());
        $this->assertNull($results->get('bar')->getSortDirection());

        $fieldCollection = new FieldCollection(array(
            'foo' => array('sortable' => true),
            'bar' => array()
        ));
        $results = $fieldCollection->setSortDirections(array('foo' => Field::SORT_DESC));
        $this->assertEquals(Field::SORT_DESC, $results->get('foo')->getSortDirection());
        $this->assertNull($results->get('bar')->getSortDirection());
    }

    public function testIterate()
    {
        $fieldCollection = new FieldCollection(array(
            new Field('foo'),
            new Field('bar', array('label' => 'baz'))
        ));

        $count = 0;

        foreach ($fieldCollection as $field) {
            $count++;
            $this->assertInstanceOf('Zenstruck\DataGridBundle\Field\Field', $field);
        }

        $this->assertEquals(2, $count);
    }

    public function testGetInvalidField()
    {
        $this->setExpectedException('InvalidArgumentException');

        $fieldCollection = new FieldCollection();
        $fieldCollection->get('foo');
    }

    public function testEmptyConstructor()
    {
        $fieldCollection = new FieldCollection();
        $this->assertCount(0, $fieldCollection);
    }

    public function testClearSorts()
    {
        $fieldCollection = new FieldCollection(array(
            new Field('foo'),
            new Field('bar', array('sortable' => true, 'sort_direction' => 'asc'))
        ));
        $this->assertEquals('asc', $fieldCollection->get('bar')->getSortDirection());

        $fieldCollection->clearSorts();
        $this->assertNull($fieldCollection->get('bar')->getSortDirection());
    }

    public function testClearFilters()
    {
        $fieldCollection = new FieldCollection(array(
            new Field('foo'),
            new Field('bar', array('filterable' => true, 'filter_value' => 'baz'))
        ));
        $this->assertEquals('baz', $fieldCollection->get('bar')->getFilterValue());

        $fieldCollection->clearFilters();
        $this->assertNull($fieldCollection->get('bar')->getFilterValue());
    }
}