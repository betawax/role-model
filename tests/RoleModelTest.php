<?php

use Mockery as m;

class RoleModelTest extends PHPUnit_Framework_TestCase {
	
	public function tearDown()
	{
		m::close();
	}
	
	public function testModelInstance()
	{
		$model = new RoleModelStub;
		$this->assertEquals('RoleModelStub', get_class($model));
		$this->assertEquals('Betawax\RoleModel\RoleModel', get_parent_class($model));
	}
	
}

class RoleModelStub extends Betawax\RoleModel\RoleModel {
	
	protected $table = 'stub';
	protected $guarded = array();
	
}
