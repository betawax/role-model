<?php namespace Betawax\RoleModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class RoleModel extends Model {
	
	/**
	 * Validation rules.
	 *
	 * @var array
	 */
	public static $rules = array();
	
	/**
	 * Validation errors.
	 *
	 * @var Illuminate\Support\MessageBag
	 */
	protected $errors;
	
	/**
	 * Validate the model's attributes.
	 *
	 * @param  array  $rules
	 * @return bool
	 */
	public function validate(array $rules = array())
	{
		$rules = $rules ? $rules : static::$rules;
		$validator = Validator::make($this->attributes, $rules);
		
		if ($validator->fails())
		{
			$this->errors = $validator->errors();
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get validation errors.
	 *
	 * @return Illuminate\Support\MessageBag
	 */
	public function errors()
	{
		return $this->errors;
	}
	
	/**
	 * Validate the model's attributes and
	 * save the model to the database.
	 *
	 * @param  array  $options
	 * @return bool
	 */
	public function save(array $options = array())
	{
		if ($this->validate())
		{
			return self::performSave($options);
		}
		
		return false;
	}
	
	/**
	 * Force save the model to the database.
	 *
	 * @param  array  $options
	 * @return bool
	 */
	public function forceSave(array $options = array())
	{
		return self::performSave($options);
	}
	
	/**
	 * Implementation of Eloquent's save method.
	 *
	 * @param  array  $options
	 * @return bool
	 */
	protected function performSave(array $options = array())
	{
		return parent::save($options);
	}
	
}
