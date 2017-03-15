<?php

namespace Techart\IO\FS;

/**
 * Условия выборки элементов каталога
 *
 * Временная реализация с минимальной функциональностью -- поиск по регулярному выражению
 * или шаблону с возможностью рекурсии. Планируется изменить реализацию для поддержки поиска
 * по датам, типам файлов и т.д.
 *
 * @property $regexp    регулярное выражение
 * @property $recursive признак выполнения рекурсивного поиска
 */
class Query implements \Techart\Core\PropertyAccessInterface
{
	const DEFAULT_REGEXP = '{.+}';

	protected $regexp = self::DEFAULT_REGEXP;
	protected $recursive = false;
	protected $self = false;

	/**
	 * Задает регулярное выражение для поиска
	 *
	 * @param string $regexp
	 *
	 * @return \Techart\IO\FS\Query
	 */
	public function regexp($regexp)
	{
		$this->regexp = (string)$regexp;
		return $this;
	}

	/**
	 * Задает шаблон поиска с использованием * и ?
	 *
	 * @param string $wildcard
	 *
	 * @return \Techart\IO\FS\Query
	 */
	public function glob($wildcard)
	{
		$this->regexp = '{' . \Techart\Core\Strings::replace(\Techart\Core\Strings::replace(\Techart\Core\Strings::replace($wildcard, '.', '\.'), '?', '.'), '*', '.*') . '}';
		return $this;
	}

	/**
	 * Устанавливает флаг рекурсивного поиска в каталоге
	 *
	 * @param boolean $use_recursion
	 * @param bool    $self
	 *
	 * @return \Techart\IO\FS\Query
	 */
	public function recursive($use_recursion = true, $self = false)
	{
		$this->recursive = (boolean)$use_recursion;
		$this->self = $self;
		return $this;
	}

	/**
	 * Возвращает итератор, соответствующий условиям поиска
	 *
	 * @param \Techart\IO\FS\Dir $dir
	 *
	 * @return \Techart\IO\FS\DirIterator
	 */
	public function apply_to(\Techart\IO\FS\Dir $dir)
	{
		if ($this->recursive) {
			if (!$this->self) {
				return new \RecursiveIteratorIterator(new \Techart\IO\FS\DirIterator($dir, $this));
			} else {
				return new \RecursiveIteratorIterator(new \Techart\IO\FS\DirIterator($dir, $this), \RecursiveIteratorIterator::SELF_FIRST);
			}
		} else {
			return new \Techart\IO\FS\DirIterator($dir, $this);
		}
	}

	/**
	 * Проверяет, является ли поиск рекурсивным
	 *
	 * @return boolean
	 */
	public function is_recursive()
	{
		return $this->recursive;
	}

	/**
	 * Проверяет, соответствует ли заданный путь условиям поиска
	 *
	 * @param string $path
	 *
	 * @return boolean
	 */
	public function allows($path = '.')
	{
		if (\Techart\IO\FS::is_dir($path) && $this->recursive) {
			return true;
		}

		return \Techart\Core\Regexps::match($this->regexp, (string)$path);
	}

	/**
	 * Проверяет отсутствие сооответствия заданного пути условиям поиска
	 *
	 * @param string $path
	 *
	 * @return boolean
	 */
	public function forbids($path = '.')
	{
		return !$this->allows($path);
	}

	/**
	 * Возвращает значение свойства
	 *
	 * @param string $property
	 *
	 * @return mixed
	 * @throws \Techart\Core\MissingPropertyException
	 */
	public function __get($property)
	{
		switch ($property) {
			case 'regexp':
			case 'recursive':
				return $this->$property;
			default:
				throw new \Techart\Core\MissingPropertyException($property);
		}
	}

	/**
	 * Устанавливает значение свойства
	 *
	 * @param string $property
	 * @param        $value
	 *
	 * @return \Techart\IO\FS\Query
	 * @throws \Techart\Core\MissingPropertyException
	 */
	public function __set($property, $value)
	{
		switch ($property) {
			case 'regexp':
				$this->regexp = (string)$value;
				break;
			case 'recursive':
				$this->recursive = (boolean)$value;
				break;
			default:
				throw new \Techart\Core\MissingPropertyException($property);
		}
		return $this;
	}

	/**
	 * Проверяет установку значения свойства
	 *
	 * @param string $property
	 *
	 * @return boolean
	 */
	public function __isset($property)
	{
		switch ($property) {
			case 'regexp':
			case 'recursive':
				return isset($this->$property);
			default:
				return false;
		}
	}

	/**
	 * Удаляет свойство
	 *
	 * @param string $property
	 *
	 * @throws \Techart\Core\MissingPropertyException
	 * @throws \Techart\Core\UndestroyablePropertyException
	 */
	public function __unset($property)
	{
		switch ($property) {
			case 'regexp':
			case 'recursive':
				throw new \Techart\Core\UndestroyablePropertyException($property);
			default:
				throw new \Techart\Core\MissingPropertyException($property);
		}
	}

}
