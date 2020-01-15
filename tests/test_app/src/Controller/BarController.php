<?php
namespace App\Controller;

class BarController extends AppController {

	/**
	 * @var string
	 */
	protected $modelClass = 'BarBars';

	/**
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();

		$this->loadComponent('Flash');
		$this->loadComponent('MyNamespace/MyPlugin.My');

		$this->loadModel('Wheels');
	}

	/**
	 * @return \Cake\Http\Response|null|void
	 */
	public function index() {
		$this->paginate($this->BarBars);
	}

}
