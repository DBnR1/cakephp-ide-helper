<?php
namespace App\Shell;

use Cake\Console\Shell;

class MyShell extends Shell {

	/**
	 * @var string
	 */
	protected $modelClass = 'Cars';

	/**
	 * @var array
	 */
	public $tasks = [
		'Command',
	];

	/**
	 * @return void
	 */
	public function main() {
		$this->loadModel('Wheels');
	}

}
