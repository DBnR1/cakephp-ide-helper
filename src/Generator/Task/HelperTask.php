<?php

namespace IdeHelper\Generator\Task;

use Cake\Filesystem\Folder;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\App;
use IdeHelper\Utility\AppPath;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\ClassName;

class HelperTask implements TaskInterface {

	/**
	 * @var string[]
	 */
	protected $aliases = [
		'\Cake\View\View::loadHelper(0)',
	];

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
		$map = [];

		$helpers = $this->collectHelpers();
		foreach ($helpers as $name => $className) {
			$map[$name] = ClassName::create($className);
		}

		ksort($map);

		$result = [];
		foreach ($this->aliases as $alias) {
			$directive = new Override($alias, $map);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return string[]
	 */
	protected function collectHelpers(): array {
		$helpers = [];

		$folders = array_merge(App::core('View/Helper'), AppPath::get('View/Helper'));
		foreach ($folders as $folder) {
			$helpers = $this->addHelpers($helpers, $folder);
		}

		$plugins = Plugin::loaded();
		foreach ($plugins as $plugin) {
			$folders = AppPath::get('View/Helper', $plugin);
			foreach ($folders as $folder) {
				$helpers = $this->addHelpers($helpers, $folder, $plugin);
			}
		}

		return $helpers;
	}

	/**
	 * @param array $helpers
	 * @param string $folder
	 * @param string|null $plugin
	 *
	 * @return string[]
	 */
	protected function addHelpers(array $helpers, $folder, $plugin = null) {
		$folderContent = (new Folder($folder))->read(Folder::SORT_NAME, true);

		foreach ($folderContent[1] as $file) {
			preg_match('/^(.+)Helper\.php$/', $file, $matches);
			if (!$matches) {
				continue;
			}
			$name = $matches[1];
			if ($plugin) {
				$name = $plugin . '.' . $name;
			}

			$className = App::className($name, 'View/Helper', 'Helper');
			if (!$className) {
				continue;
			}

			$helpers[$name] = $className;
		}

		return $helpers;
	}

}
