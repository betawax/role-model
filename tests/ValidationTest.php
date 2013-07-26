<?php

use Betawax\RoleModel\RoleModel;
use Mockery as m;

class ValidationTest extends PHPUnit_Framework_TestCase {
	
	public function tearDown()
	{
		m::close();
	}
	
	public function testValidatePasses()
	{
		$model = new RoleModelValidationStub(array(), self::validatorPassMock());
		$result = $model->validate();
		
		$this->assertTrue($result);
		$this->assertFalse($model->hasErrors());
		$this->assertNull($model->errors());
	}
	
	public function testValidateFails()
	{
		$model = new RoleModelValidationStub(array(), self::validatorFailMock());
		$result = $model->validate();
		
		$this->assertFalse($result);
		$this->assertTrue($model->hasErrors());
		$this->assertNotNull($model->errors());
	}
	
	public function testProcessRules()
	{
		$model = new RoleModelValidationStub(array(), self::validatorMock());
		$rules = RoleModelValidationStub::$rules;
		
		$processRules = new ReflectionMethod($model, 'processRules');
		$processRules->setAccessible(true);
		
		$expected = array('name' => 'required', 'email' => 'unique:foobar,email,');
		$result = $processRules->invokeArgs($model, array($rules));
		
		$this->assertEquals($expected, $result);
		
		$model->id = 42;
		
		$expected = array('name' => 'required', 'email' => 'unique:foobar,email,42');
		$result = $processRules->invokeArgs($model, array($rules));
		
		$this->assertEquals($expected, $result);
	}
	
	public function testValidator()
	{
		$validator = self::validatorPassMock();
		$model = new RoleModelValidationStub(array(), $validator);
		
		$this->assertNull($model->validator());
		
		$model->validate();
		
		$this->assertNotNull($model->validator());
	}
	
	public function testErrors()
	{
		$model = new RoleModelValidationStub(array(), self::validatorMock());
		
		$this->assertNull($model->errors());
		
		$errors = new ReflectionProperty($model, 'errors');
		$errors->setAccessible(true);
		$errors->setValue($model, new Illuminate\Support\MessageBag);
		
		$this->assertNotNull($model->errors());
		$this->assertInstanceOf('Illuminate\Support\MessageBag', $model->errors());
	}
	
	public function testHasErrors()
	{
		$model = new RoleModelValidationStub(array(), self::validatorMock());
		
		$this->assertFalse($model->hasErrors());
		
		$model = new RoleModelValidationStub(array(), self::validatorPassMock());
		$model->validate();
		
		$this->assertFalse($model->hasErrors());
		
		$model = new RoleModelValidationStub(array(), self::validatorFailMock());
		$model->validate();
		
		$this->assertTrue($model->hasErrors());
	}
	
	protected function validatorMock()
	{
		return m::mock('Illuminate\Validation\Factory');
	}
	
	protected function validatorPassMock()
	{
		$response = m::mock('StdClass');
		$response->shouldReceive('fails')->once()->andReturn(false);
		
		$validator = self::validatorMock();
		$validator->shouldReceive('make')->once()->andReturn($response);
		
		return $validator;
	}
	
	protected function validatorFailMock()
	{
		$response = m::mock('StdClass');
		$response->shouldReceive('fails')->once()->andReturn(true);
		$response->shouldReceive('errors')->once()->andReturn('foobar');
		
		$validator = self::validatorMock();
		$validator->shouldReceive('make')->once()->andReturn($response);
		
		return $validator;
	}
	
}

class RoleModelValidationStub extends RoleModel {
	
	public static $rules = array(
		'name'  => 'required',
		'email' => 'unique:foobar,email,:id:',
	);
	
}
