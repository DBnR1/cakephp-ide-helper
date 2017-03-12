#  CakePHP IdeHelper Plugin Documentation

Note that freshly [baking](https://github.com/cakephp/bake) your code will result in similar results,
but often times there is already existing code, and re-baking it is not an option.


## Controllers
All controllers should at least annotate their primary model.

```
bin/cake annotations controllers
```

### Primary model via convention
```php
<?php
namespace App\Controller;

class ApplesController extends AppController {
}
```
becomes
```php
<?php
namespace App\Controller;

/**
 * @property \App\Model\Table\ApplesTable $Apples
 */
class ApplesController extends AppController {
}
```
You get autocompletion on any `$this->Apples->...()` usage in your controllers then.

Use `-p PluginName` to annotate inside a plugin. It will then use the plugin name as namespace.

### Primary model via $modelClass definition
When defining `$modelClass` it will be used instead:
```php
<?php
namespace App\Controller;

/**
 * @property \App\Model\Table\MyApplesTable $MyApples
 */
class ApplesController extends AppController {

	public $modelClass = 'MyApples';

}
```

## Models
This will ensure the annotations for tables and their entities:

```
bin/cake annotations models
```

### Tables
Tables should annotate their entity related methods, their relations and behavior mixins.

A LocationsTable class would then get the following doc block annotations added if not already present:
```php
/**
 * @method \App\Model\Entity\Location get($primaryKey, $options = [])
 * @method \App\Model\Entity\Location newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Location[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Location|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Location patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Location[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Location findOrCreate($search, callable $callback = null, $options = [])
```

### Entities
Entities should annotate their fields and relations.

A Location entity could look like this afterwards:
```php
/**
 * @property int $id
 * @property int $user_id
 * @property \App\Model\Entity\User $user
 * @property string $location
 * @property string $details
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \App\Model\Entity\Image[] $images
 */
class Location extends Entity {
}
```

## Shells
Shells and Tasks should annotate their primary model as well as all manually loaded models.

```
bin/cake annotations shells
```

```php
	/**
	 * @var string
	 */
	public $modelClass = 'Cars';

	/**
	 * @return void
	 */
	public function main() {
		$this->loadModel('MyPlugin.Wheels');
	}
```
will result in the following annotation:

```php
/**
 * @property \MyPlugin\Model\Table\WheelsTable $Wheels
 * @property \App\Model\Table\CarsTable $Cars
 */
```

## View
The AppView class should annotate the helpers of the plugins and the app.

```
bin/cake annotations view
```

With template content like
```html
<?php echo $this->My->foo($bar); ?>
<?php if ($this->Configure->baz()) {} ?>
```
the following would be annotated (if `My` and `Shim.Configure` helpers were loaded correctly):
```php
/**
 * @property \App\View\Helper\MyHelper $My
 * @property \Shim\View\Helper\ConfigureHelper $Configure
 */
class AppView extends View {
} 
```

## Components
Components should annotate any component they use.

```
bin/cake annotations components
```

A component containing
```php
	/**
	 * @var array
	 */
	public $helpers = [
		'RequestHandler',
		'Flash.Flash',
	];
```
would get the following annotations:
```php
/**
 * @property \App\Controller\Component\RequestHandlerComponent $RequestHandler
 * @property \Flash\Controller\Component\FlashComponent $Flash
 */
```

## Helpers
Helpers should annotate any helper they use.

```
bin/cake annotations helpers
```

A helper containing
```php
	/**
	 * @var array
	 */
	public $helpers = [
		'Form',
	];

	/**
	 * @param \Cake\View\View $View
	 * @param array $config
	 */
	public function __construct(View $View, array $config = []) {
		parent::__construct($View, $config);
		$this->_View->loadHelper('Template');
	}
```
would get the following annotations:
```php
/**
 * @property \Cake\View\Helper\FormHelper $Form
 * @property \App\View\Helper\TemplateHelper $Template
 */
```

## Templates
This will ensure annotations for view templates and elements:
```
bin/cake annotations templates
```
Templates should have a `/** @var \App\View\AppView $this */` added on top if they use any helper or access the request object.
They should also annotate entities they use.

A template such as
```html
<h2>Some header</h2>
<?php echo $this->Form->create($user); ?>
<?php foreach ($groups as $group): ?>
<?php endforeach; ?>
<li><?= $this->Html->link(__('Edit Email'), ['action' => 'edit', $email->id]) ?> </li>
```
would then get the following added on top:
```php
<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Email $email
 * @var \App\Model\Entity\Group[] $groups
 */
?>
```

## Writing your own annotators
Just extend the shell on application level, add your command and create your own Annotator class:
```php
class MyAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path
	 * @return bool
	 */
	public function annotate($path) {
	}
}
```
Then read a folder, iterate over it and invoke your annotator from the shell command with a specific path.