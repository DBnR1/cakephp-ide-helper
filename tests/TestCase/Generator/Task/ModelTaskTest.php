<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\ModelTask;

class ModelTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\ModelTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->task = new ModelTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(3, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\ORM\TableRegistry::get(0)', $directive->toArray()['method']);

		$map = $directive->toArray()['map'];

		$expectedMap = [
			'Abstract' => '\App\Model\Table\AbstractTable::class',
			'Awesome.Houses' => '\Awesome\Model\Table\HousesTable::class',
			'Awesome.Windows' => '\Awesome\Model\Table\WindowsTable::class',
			'BarBars' => '\App\Model\Table\BarBarsTable::class',
			'BarBarsAbstract' => '\App\Model\Table\BarBarsAbstractTable::class',
			'Callbacks' => '\App\Model\Table\CallbacksTable::class',
			'Cars' => '\App\Model\Table\CarsTable::class',
			'Controllers.Houses' => '\Controllers\Model\Table\HousesTable::class',
			'CustomFinder' => '\App\Model\Table\CustomFinderTable::class',
			'Exceptions' => '\App\Model\Table\ExceptionsTable::class',
			'Foo' => '\App\Model\Table\FooTable::class',
			'MyNamespace/MyPlugin.My' => '\MyNamespace\MyPlugin\Model\Table\MyTable::class',
			'SkipMe' => '\App\Model\Table\SkipMeTable::class',
			'SkipSome' => '\App\Model\Table\SkipSomeTable::class',
			'Wheels' => '\App\Model\Table\WheelsTable::class',
			'WheelsExtra' => '\App\Model\Table\WheelsExtraTable::class',
		];
		$this->assertSame($expectedMap, $map);
	}

}
