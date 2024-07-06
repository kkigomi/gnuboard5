<?php
use Damoang\Lib\G5\G5CommonObject;

class G5CommonObjectTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testInstance()
    {
        $object = new class (null) extends G5CommonObject
        {
            protected $defaults = [
                'exists_key' => 'value',
            ];
        };
        $this->assertInstanceOf(G5CommonObject::class, $object);
        $this->assertArrayHasKey('exists_key', $object);
        $this->assertArrayNotHasKey('unknown', $object);
    }

    public function testTypeCasting()
    {
        $data = [
            'int_value' => '123',
            'integer_value' => '456',
            'float_value' => '123.456',
            'double_value' => '456.789',
            'bool_value_true' => '1',
            'bool_value_false' => '0',
            'boolean_value' => '1',
            'filter_bool_value_true' => 'true',
            'filter_bool_value_false' => 'unknown',
        ];

        $object = new class ($data) extends G5CommonObject
        {
            protected $casts = [
                'int_value' => 'int',
                'integer_value' => 'integer',
                'float_value' => 'float',
                'double_value' => 'double',
                'bool_value_true' => 'bool',
                'bool_value_false' => 'bool',
                'boolean_value' => 'boolean',
                'filter_bool_value_true' => 'filter_bool',
                'filter_bool_value_false' => 'filter_bool',
            ];
        };

        $this->assertSame(123, $object['int_value']);
        $this->assertSame(456, $object['integer_value']);
        $this->assertSame(123.456, $object['float_value']);
        $this->assertSame(456.789, $object['double_value']);
        $this->assertTrue($object['bool_value_true']);
        $this->assertFalse($object['bool_value_false']);
        $this->assertTrue($object['boolean_value']);
        $this->assertTrue($object['filter_bool_value_true']);
        $this->assertFalse($object['filter_bool_value_false']);

        $object['boolean_value'] = '0';
        $this->assertNotTrue($object['boolean_value']);
    }

}