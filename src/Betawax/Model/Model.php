<?php namespace Betawax\Model;

use Illuminate\Support\Facades\Validator;

class Model extends \Illuminate\Database\Eloquent\Model {
	
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
	 * Override Eloquent's save method.
	 *
	 * @param  array  $options
	 * @return bool
	 */
	public function save(array $options = array())
	{
		if ($this->validate())
		{
			return parent::save($options);
		}
		
		return false;
	}
	
}
