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
		$model = new RoleModelStub(array(), $validator);
		
		$this->assertInstanceOf('RoleModelStub', $model);
		$this->assertInstanceOf('Betawax\RoleModel\RoleModel', $model);
	}
	
	public function testValidatePasses()
	{
		$response = m::mock('StdClass');
		$response->shouldReceive('fails')->once()->andReturn(false);
		
		$validator = m::mock('Illuminate\Validation\Validator');
		$validator->shouldReceive('make')->once()->andReturn($response);
		
		$model = new RoleModelStub(array(), $validator);
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
		
		$model = new RoleModelStub(array(), $validator);
		$result = $model->validate();
		
		$this->assertFalse($result);
		$this->assertEquals('foobar', $model->errors());
	}
	
	public function testErrors()
	{
		$validator = m::mock('Illuminate\Validation\Validator');
		$model = new RoleModelStub(array(), $validator);
		
		$this->assertNull($model->errors());
		
		$reflectionModel = new ReflectionObject($model);
		$errors = $reflectionModel->getProperty('errors');
		$errors->setAccessible(true);
		$errors->setValue($model, 'foobar');
		
		$this->assertEquals('foobar', $model->errors());
	}
	
	public function testHasErrors()
	{
		$validator = m::mock('Illuminate\Validation\Validator');
		$model = new RoleModelStub(array(), $validator);
		
		$this->assertFalse($model->hasErrors());
		
		$response = m::mock('StdClass');
		$response->shouldReceive('fails')->once()->andReturn(false);
		
		$validator = m::mock('Illuminate\Validation\Validator');
		$validator->shouldReceive('make')->once()->andReturn($response);
		
		$model = new RoleModelStub(array(), $validator);
		$model->validate();
		
		$this->assertFalse($model->hasErrors());
		
		$response = m::mock('StdClass');
		$response->shouldReceive('fails')->once()->andReturn(true);
		$response->shouldReceive('errors')->once()->andReturn('foobar');
		
		$validator = m::mock('Illuminate\Validation\Validator');
		$validator->shouldReceive('make')->once()->andReturn($response);
		
		$model = new RoleModelStub(array(), $validator);
		$model->validate();
		
		$this->assertTrue($model->hasErrors());
	}
	
}

class RoleModelStub extends RoleModel {
	
	protected $table = 'stub';
	protected $guarded = array();
	
}
