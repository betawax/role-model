<?php namespace Betawax\RoleModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

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
	 * The Validator instance.
	 *
	 * @var Illuminate\Validation\Validator
	 */
	protected $validator;
	
	/**
	 * Indicates if the model should be saved without validation.
	 *
	 * @var bool
	 */
	protected $force = false;
	
	/**
	 * Create a new RoleModel instance.
	 *
	 * @param  array  $attributes
	 * @param  Illuminate\Validation\Validator  $validator
	 * @return void
	 */
	public function __construct(array $attributes = array(), Validator $validator = null)
	{
		parent::__construct($attributes);
		
		$this->validator = $validator ? $validator : \App::make('validator');
	}
	
	/**
	 * Register event bindings.
	 *
	 * @return void
	 */
	public static function boot()
	{
		parent::boot();
		
		static::saving(function($model)
		{
			if ( ! $model->isForced()) return $model->validate();
		});
	}
	
	/**
	 * Validate the model's attributes.
	 *
	 * @param  array  $rules
	 * @return bool
	 */
	public function validate(array $rules = array())
	{
		$rules = $this->processRules($rules ? $rules : static::$rules);
		$this->validator = $this->validator->make($this->attributes, $rules);
		
		if ($this->validator->fails())
		{
			$this->errors = $this->validator->errors();
			return false;
		}
		
		$this->errors = null;
		return true;
	}
	
	/**
	 * Process validation rules.
	 *
	 * @param  array  $rules
	 * @return array  $rules
	 */
	protected function processRules(array $rules)
	{
		$id = $this->getKey();
		array_walk($rules, function(&$item) use ($id)
		{
			// Replace placeholders
			$item = stripos($item, ':id:') !== false ? str_ireplace(':id:', $id, $item) : $item;
		});
		
		return $rules;
	}
	
	/**
	 * Get the Validator instance.
	 *
	 * @return Illuminate\Validation\Validator
	 */
	public function validator()
	{
		return $this->validator;
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
	 * Check if the model has validation errors.
	 *
	 * @return bool
	 */
	public function hasErrors()
	{
		return ! is_null($this->errors);
	}
	
	/**
	 * Save the model to the database.
	 *
	 * @param  array  $options
	 * @return bool
	 */
	public function save(array $options = array())
	{
		$this->force = false;
		return parent::save($options);
	}
	
	/**
	 * Force save the model to the database.
	 *
	 * @param  array  $options
	 * @return bool
	 */
	public function forceSave(array $options = array())
	{
		$this->force = true;
		return parent::save($options);
	}
	
	/**
	 * Determine if the model instance should be saved without validation.
	 *
	 * @return bool
	 */
	public function isForced()
	{
		return $this->force;
	}
	
}
