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
	
}

class RoleModelStub extends RoleModel {
	
	protected $table = 'stub';
	protected $guarded = array();
	
}
