<?php
namespace TestApp\Shell;

use Cake\Console\Shell;

class MyShell extends Shell {

	/**
	 * @var string
	 */
	public $modelClass = 'Cars';

	/**
	 * @var array
	 */
	public $tasks = [
		'Assets',
	];

	/**
	 * @return void
	 */
	public function main() {
		$this->loadModel('Wheels');
	}

}
