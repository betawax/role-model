<?php namespace Betawax\RoleModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Factory as Validator;

class RoleModel extends Model {


	/**
	 * Determines if the rules array keys are being used for the fillable array.
	 *
	 * @var bool
     */
	protected $useRulesAsFillable = false;

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
	 * The Validator factory instance.
	 *
	 * @var Illuminate\Validation\Factory
	 */
	protected $validatorFactory;
	
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
	 * @param  Illuminate\Validation\Factory  $validator
	 * @return void
	 */
	public function __construct(array $attributes = array(), Validator $validator = null)
	{
		parent::__construct($attributes);

		/**
		 * Generate fillable array from rules arrays keys.
		 */
		if($this->useRulesAsFillable)
		{
			$className = get_class($this);
			$this->fillable = array_keys($className::$rules);
		}
		
		$this->validatorFactory = $validator ?: \App::make('validator');
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
			if ( ! $model->isForced())
                if($model->validate() === false)
                    return false;
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
		$rules = $this->processRules($rules ?: static::$rules);
		$this->validator = $this->validatorFactory->make($this->attributes, $rules);
		
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
			if(!is_array($item))
				$item = $this->processRule($item, $id);
			else
				array_walk($item, function(&$subItem) use ($id){
					$subItem = $this->processRule($subItem, $id);
				});
		});
		
		return $rules;
	}

	/**
	 * Processes a single validation rule.
	 * 
	 * @param $item
	 * @param $id
	 * @return mixed
	 */
	protected function processRule(&$item, $id)
	{
		return stripos($item, ':id:') !== false ? str_ireplace(':id:', $id, $item) : $item;
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
