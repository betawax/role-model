# Role Model [![Latest Stable Version](https://poser.pugx.org/betawax/role-model/v/stable.png)](https://packagist.org/packages/betawax/role-model) [![Build Status](https://travis-ci.org/betawax/role-model.png?branch=master)](https://travis-ci.org/betawax/role-model) #

Role Model adds some extra functionality to your Laravel 4 Eloquent models, currently focusing on validation. Read the following documentation to get started.

## Table of contents

- [Installation](#installation)
- [Usage](#usage)
	- [Validation](#validation)
- [Changelog](#changelog)
- [License](#license)

## Installation

Install the package via Composer by requiring it in your `composer.json`:

	"require": {
		"betawax/role-model": "~1.0"
	}

Don't forget to run `composer install` afterwards.

**Heads up!** Use `~1.0` to get only stable releases until the next major release or `dev-master` to stay up to date with the latest commits in the master branch.

---

Now rather than extending `Eloquent` in your model, extend `Betawax\RoleModel\RoleModel` instead:

	class Foobar extends Betawax\RoleModel\RoleModel {
		
	}

For your convenience, I recommend to edit your `app/config/app.php` and add `Betawax\RoleModel\RoleModel` to the `aliases` array:

	'aliases' => array(
		'RoleModel' => 'Betawax\RoleModel\RoleModel'
	)

Now you can simply extend `RoleModel` in your model:

	class Foobar extends RoleModel {
		
	}

That's it. Since Role Model extends from Eloquent, you don't have to change anything else in your model. You now can start to use the extra functionality described in the usage section below.

## Usage

### Validation

Role Model allows validation to take place in the model rather than the controller. You simply specify validation rules in your model and Role Model then auto-validates your model on each save. The validation itself is done via Laravel's [Validation](http://four.laravel.com/docs/validation) facility.

#### Defining validation rules

Define validation rules for your model via the static `$rules` array:

	class Foobar extends RoleModel {
		
		public static $rules = array(
			'name'  => 'required',
			'email' => 'unique:foobar,email,:id:',
		);
		
	}

See the validation section in the Laravel documentation for a list of all available [validation rules](http://four.laravel.com/docs/validation#available-validation-rules).

**Heads up!** Note that in the example above `:id:` is a placeholder that automatically gets replaced by the value of your model's primary key before every validation. This allows the usage of the [unique validation rule](http://four.laravel.com/docs/validation#rule-unique) when updating your model. You're welcome.

#### Auto-validate on save

Role Model uses Eloquent's [model events](http://four.laravel.com/docs/eloquent#model-events) to hook into your model's lifecycle and auto-validate the model on each save. An example implementation would be:

	public function store()
	{
		$model = new Foobar;
		$model->name = 'foobar';
		
		if ($model->save())
		{
			// Validation passed
			return Redirect::action('FoobarController@index');
		}
		
		// Validation failed, errors are available via $model->errors()
		return Redirect::action('FoobarController@create')->withInput()->withErrors($model->errors());
	}

#### Retrieving validation errors

You retrieve validation errors via the `errors()` getter:

	$model->errors() // Instance of MessageBag or null

Like using Laravel's `Validation` class directly, the return value from `errors()` will be an instance of `MessageBag` or `null` if there are no validation errors.

#### Check for validation errors

To check if a model has validation errors, you use the `hasErrors()` method:

	$model->hasErrors() // true or false

#### Validate without saving

If you just want to validate without saving, you can use the `validate()` method directly:

	$model->validate() // true or false

#### Validate with custom rules

You can also validate with custom rules by simply passing it to the `validate()` method:

	$rules = array(
		'name' => 'required|min:5'
	);
	
	$model->validate($rules);

#### Retrieving validation rules

You can retrieve your model's validation rules without the need to instantiate the whole model:

	$model::$rules // array

#### Access the Validator instance

You are free to access the Validator instance after validation by using the `validator` getter:

	$model->validate(); // or $model->save()
	$validator = $model->validator(); // Illuminate\Validation\Validator
	$messages = $validator->messages();

#### Force save without validation

If you want to force save your model without validation, simply use the `forceSave()` method instead of `save()`:

	$model->forceSave()

## Changelog

### Version 1.0.3 (27.07.2013)

- Add `validator` and `isForced` methods
- Improve unit tests and test against PHP 5.5
- Fix `forceSave`, see issue [#1](https://github.com/betawax/role-model/issues/1)

### Version 1.0.2 (13.06.2013)

- Fix closure mistake introduced in 1.0.1

### Version 1.0.1 (13.06.2013)

- Fix object context error in PHP 5.3

### Version 1.0.0 (13.05.2013)

- Initial release

## License

Licensed under the MIT license.
