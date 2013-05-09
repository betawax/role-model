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
	
	public function testErrors()
	{
		$model = new RoleModelValidationStub(array(), self::validatorMock());
		
		$this->assertNull($model->errors());
		
		$reflectionModel = new ReflectionObject($model);
		$errors = $reflectionModel->getProperty('errors');
		$errors->setAccessible(true);
		$errors->setValue($model, 'foobar');
		
		$this->assertNotNull($model->errors());
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
		return m::mock('Illuminate\Validation\Validator');
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
	
	protected $table = 'validation_stub';
	protected $guarded = array();
	
}
