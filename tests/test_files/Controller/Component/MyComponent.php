<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * @property \Cake\Controller\Component\FlashComponent $Flash
 * @property \App\Controller\Component\RequestHandlerComponent $RequestHandler
 * @property \MyNamespace\MyPlugin\Controller\Component\MyComponent $My
 */
class MyComponent extends Component {

	/**
	 * @var array
	 */
	public $components = ['Flash', 'RequestHandler', 'MyNamespace/MyPlugin.My'];

}
