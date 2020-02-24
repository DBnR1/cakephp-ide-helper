<?php

namespace IdeHelper\Annotator;

use Cake\Core\App;
use Exception;
use IdeHelper\Annotation\AnnotationFactory;
use IdeHelper\Annotation\PropertyAnnotation;
use Throwable;

class ShellAnnotator extends AbstractAnnotator {

	/**
	 * @param string $path Path to file.
	 * @return bool
	 */
	public function annotate($path) {
		$className = pathinfo($path, PATHINFO_FILENAME);
		if ($className === 'Shell' || substr($className, -5) !== 'Shell' && substr($className, -4) !== 'Task') {
			return false;
		}

		$content = file_get_contents($path);
		$primaryModelClass = $this->_getPrimaryModelClass($content);
		$usedModels = $this->_getUsedModels($content);
		if ($primaryModelClass) {
			$usedModels[] = $primaryModelClass;
		}
		$usedModels = array_unique($usedModels);

		$annotations = $this->_getModelAnnotations($usedModels, $content);

		$usedTasks = $this->_getUsedTasks($className);
		foreach ($usedTasks as $alias => $usedTask) {
			$annotations[] = AnnotationFactory::createOrFail(PropertyAnnotation::TAG, '\\' . $usedTask['fullClass'], '$' . $alias);
		}

		return $this->_annotate($path, $content, $annotations);
	}

	/**
	 * @param string $content
	 *
	 * @return string|null
	 */
	protected function _getPrimaryModelClass($content) {
		if (!preg_match('/\bpublic \$modelClass = \'([a-z.\/]+)\'/i', $content, $matches)) {
			return null;
		}

		$modelName = $matches[1];

		return $modelName;
	}

	/**
	 * @param string $content
	 *
	 * @return string[]
	 */
	protected function _getUsedModels($content) {
		preg_match_all('/\$this->loadModel\(\'([a-z.\/]+)\'/i', $content, $matches);
		if (empty($matches[1])) {
			return [];
		}

		$models = $matches[1];

		return array_unique($models);
	}

	/**
	 * @param string $name
	 *
	 * @throws \Exception
	 *
	 * @return array
	 */
	protected function _getUsedTasks($name) {
		$plugin = $this->getConfig(static::CONFIG_PLUGIN);
		if (substr($name, -4) === 'Task') {
			$className = App::className(($plugin ? $plugin . '.' : '') . $name, 'Shell/Task');
		} else {
			$className = App::className(($plugin ? $plugin . '.' : '') . $name, 'Shell');
		}
		if (!$className) {
			throw new Exception($name);
		}

		if ($this->_isAbstract($className)) {
			return [];
		}

		try {
			/** @var \Cake\Console\Shell $object */
			$object = new $className();
			$object->loadTasks();
		} catch (Exception $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping shell task annotations: ' . $e->getMessage());
			}
			return [];
		} catch (Throwable $e) {
			if ($this->getConfig(static::CONFIG_VERBOSE)) {
				$this->_io->warn('   Skipping shell task annotations: ' . $e->getMessage());
			}
			return [];
		}

		$map = $this->_invokeProperty($object, '_taskMap');
		if (!$map) {
			return [];
		}
		foreach ($map as $alias => $row) {
			$fullClass = App::className($row['class'], 'Shell/Task', 'Task');
			if (!$fullClass) {
				$this->_io->warn('   Skipping invalid task ' . $alias . ': ' . $row['class']);
				unset($map[$alias]);
				continue;
			}
			$map[$alias]['fullClass'] = $fullClass;
		}

		return $map;
	}

}
