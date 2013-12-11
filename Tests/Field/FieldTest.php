<?php

namespace Zenstruck\DataGridBundle\Tests\Field;

use Zenstruck\DataGridBundle\Field\Field;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $field = new Field('foo');
        $this->assertEquals('foo', $field->getName());
        $this->assertNull($field->getLabel());
        $this->assertFalse($field->isFilterable());
        $this->assertNull($field->getFilterValue());
        $this->assertNull($field->getSortDirection());
        $this->assertTrue($field->isVisible());

        $field = new Field('foo', array(
            'label' => 'bar',
            'visible' => false,
            'filterable' => true,
            'filter_value' => 'baz',
            'sort_direction' => Field::SORT_DESC
        ));
        $this->assertEquals('foo', $field->getName());
        $this->assertEquals('bar', $field->getLabel());
        $this->assertTrue($field->isFilterable());
        $this->assertEquals('baz', $field->getFilterValue());
        $this->assertEquals(Field::SORT_DESC, $field->getSortDirection());
        $this->assertFalse($field->isVisible());
    }

    public function testInvalidOption()
    {
        $this->setExpectedException('Symfony\Component\OptionsResolver\Exception\InvalidOptionsException');
        $field = new Field('foo', array('baz' => 'bar'));
    }

    public function testSetFilterValue()
    {
        $field = new Field('foo', array(
            'filterable' => true
        ));
        $field->setFilterValue('baz');
        $this->assertEquals('baz', $field->getFilterValue());

        $field = new Field('foo');
        $field->setFilterValue('baz');
        $this->assertNull($field->getFilterValue());
    }

    public function testSetSortDirection()
    {
        $field = new Field('foo', array(
            'sortable' => true
        ));
        $field->setSortDirection(Field::SORT_DESC);
        $this->assertEquals(Field::SORT_DESC, $field->getSortDirection());

        // set non sortable field
        $field = new Field('foo');
        $field->setSortDirection(Field::SORT_DESC);
        $this->assertNull($field->getSortDirection());
    }

    public function testSetInvalidSortDirection()
    {
        $field = new Field('foo', array(
            'sortable' => true
        ));
        $field->setSortDirection('foo');
        $this->assertNull($field->getSortDirection());
    }

    public function testGetOppositeSortDirection()
    {
        $field = new Field('foo');
        $this->assertEquals(Field::SORT_DESC, $field->getOppositeSortDirection());

        $field = new Field('foo', array('sort_direction' => Field::SORT_DESC));
        $this->assertEquals(Field::SORT_ASC, $field->getOppositeSortDirection());
    }

    public function testGetValue()
    {
        $field = new Field('foo');
        $this->assertEquals('foo', $field->getValue(new Entity()));

        $field = new Field('foo_foo');
        $this->assertEquals('foo_foo', $field->getValue(new Entity()));

        $field = new Field('fooBaz');
        $this->assertEquals('fooBaz', $field->getValue(new Entity()));

        $field = new Field('fooBar');
        $this->assertEquals('getFooBar', $field->getValue(new Entity()));

        $field = new Field('foo_bar');
        $this->assertEquals('getFooBar', $field->getValue(new Entity()));

        $field = new Field('foo');
        $this->assertEquals('foo', $field->getValue(array('foo' => 'foo')));
    }
}

class Entity
{
    public $foo = 'foo';

    public $fooBaz = 'fooBaz';

    public $foo_foo = 'foo_foo';

    public function getFooBar()
    {
        return 'getFooBar';
    }
}
