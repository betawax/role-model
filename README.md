# Role Model

Advanced models for Laravel's Eloquent ORM.

## Installation

Install the package via Composer by requiring it in your `composer.json`:

	"require": {
		"betawax/role-model": "dev-master"
	}

Now rather than extending `Eloquent` in your model, extend `Betawax\RoleModel\RoleModel` instead:

	class Foobar extends Betawax\RoleModel\RoleModel {
		
	}

For your convenience, you can also edit your `app/config/app.php` and add `Betawax\RoleModel\RoleModel` to the `aliases` array:

	'aliases' => array(
		'RoleModel' => 'Betawax\RoleModel\RoleModel'
	)

Now you can simply extend `RoleModel` in your model:

	class Foobar extends RoleModel {
		
	}

## Usage

### Validation

Role Model allows validation to take place in the model rather than the controller.

#### Defining validation rules

Define validation rules for your model via the static `$rules` array:

	class Foobar extends RoleModel {
		
		public static $rules = array(
			'name'  => 'required',
			'email' => 'unique:foobar,email,:id:',
		);
		
	}

See the validation section in the Laravel documentation for a list of all available [validation rules](http://four.laravel.com/docs/validation#available-validation-rules).

**Heads up!** Note that in the example above `:id:` is a placeholder that automatically gets replaced by the value of your model's primary key before validation. This allows the use of the [unique validation rule](http://four.laravel.com/docs/validation#rule-unique) when updating your model. You're welcome.

#### Auto-validate on save

Role Model uses Eloquent's [model events](http://four.laravel.com/docs/eloquent#model-events) to hook into your model's lifecycle and auto-validate the model on each save:

	public function store()
	{
		$model = new Foobar;
		$model->name = 'foobar';
		
		if ($model->save())
		{
			return Redirect::action('FoobarController@index');
		}
		
		return Redirect::action('FoobarController@create')->withInput()->withErrors($model->errors());
	}

The validation itself is done via Laravel's `Validation` class.

#### Retrieving validation errors

You retrieve validation errors via the `errors()` getter:

	$model->errors()

Like using the `Validation` class directly, the return value from `errors()` will be an instance of `MessageBag`.

#### Check for validation errors

To check if a model has validation errors, you use the `hasErrors()` method:

	$model->hasErrors()

#### Validate without saving

If you just want to validate without saving, you can use the `validate()` method:

	$model->validate()

#### Validate with custom rules

You can also validate with custom rules by simply passing it to the `validate()` method:

	$rules = array(
		'name' => 'required|min:5'
	);
	
	$model->validate($rules);

#### Retrieving validation rules

You can retrieve your model's validation rules without the need to instantiate the whole model:

	print_r($model::$rules);

#### Force save without validation

If you want to force save your model without validation, use the `forceSave()` method instead of `save()`:

	$model->forceSave()

## License

Licensed under the MIT license.
