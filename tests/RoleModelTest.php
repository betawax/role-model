<?php

use Betawax\RoleModel\RoleModel;
use Mockery as m;

class RoleModelTest extends PHPUnit_Framework_TestCase {
	
	public function tearDown()
	{
		m::close();
	}
	
	public function testModelInstances()
	{
		$validator = m::mock('Illuminate\Validation\Validator');
		$model = new RoleModelStub([], $validator);
		
		$this->assertInstanceOf('RoleModelStub', $model);
		$this->assertInstanceOf('Betawax\RoleModel\RoleModel', $model);
	}
	
	public function testValidatePasses()
	{
		$response = m::mock('StdClass');
		$response->shouldReceive('fails')->once()->andReturn(false);
		
		$validator = m::mock('Illuminate\Validation\Validator');
		$validator->shouldReceive('make')->once()->andReturn($response);
		
		$model = new RoleModelStub([], $validator);
		$result = $model->validate();
		
		$this->assertTrue($result);
		$this->assertNull($model->errors());
	}
	
	public function testValidateFails()
	{
		$response = m::mock('StdClass');
		$response->shouldReceive('fails')->once()->andReturn(true);
		$response->shouldReceive('errors')->once()->andReturn('foobar');
		
		$validator = m::mock('Illuminate\Validation\Validator');
		$validator->shouldReceive('make')->once()->andReturn($response);
		
		$model = new RoleModelStub([], $validator);
		$result = $model->validate();
		
		$this->assertFalse($result);
		$this->assertEquals('foobar', $model->errors());
	}
	
	public function testErrors()
	{
		$validator = m::mock('Illuminate\Validation\Validator');
		$model = new RoleModelStub([], $validator);
		
		$this->assertNull($model->errors());
		
		$reflectionModel = new ReflectionObject($model);
		$errors = $reflectionModel->getProperty('errors');
		$errors->setAccessible(true);
		$errors->setValue($model, 'foobar');
		
		$this->assertEquals('foobar', $model->errors());
	}
	
}

class RoleModelStub extends RoleModel {
	
	protected $table = 'stub';
	protected $guarded = array();
	
}
