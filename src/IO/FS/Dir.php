<?php

namespace Techart\IO\FS;

/**
 * Каталог файловой системы
 *
 * Расширяет базовый класс файлового объекта следующими возможностями:
 * - получение объектного представления элементов каталога с помощью операции доступа по индексу
 * - итератор по элементам каталога
 * - овместная работа с объектами запроса списка файлов, позволяющих запрашивать
 *   списки элементов каталога, удовлетворяющих определенным критериям.
 *
 * @property $files итератор по содержимому каталога с настройками по умолчанию
 *
 * @package IO\FS
 */
class Dir extends FSObject implements
	\Techart\Core\PropertyAccessInterface,
	\Techart\Core\IndexedAccessInterface,
	\IteratorAggregate
{
	/**
	 * Конструктор
	 *
	 * @param string $path
	 */
	public function __construct($path)
	{
		parent::__construct(rtrim($path, '/'));
	}

	/**
	 * Применяет к каталогу объект, содержащий условия запроса его содержимого
	 *
	 * @param \Techart\IO\FS\Query $query
	 *
	 * @return \Techart\IO\FS\Dir
	 */
	public function query(\Techart\IO\FS\Query $query)
	{
		return $query->apply_to($this);
	}

	/**
	 * Возвращает значение свойства
	 *
	 * @param string $property
	 *
	 * @return mixed
	 */
	public function __get($property)
	{
		switch ($property) {
			case 'files':
				return $this->make_default_iterator();
			default:
				return parent::__get($property);
		}
	}

	/**
	 * Устанавливает значение свойства
	 *
	 * @param string $property
	 * @param        $value
	 *
	 * @return mixed
	 * @throws \Techart\Core\MissingPropertyException
	 * @throws \Techart\Core\ReadOnlyObjectException
	 * @throws \Techart\Core\ReadOnlyPropertyException
	 */
	public function __set($property, $value)
	{
		switch ($property) {
			case 'files':
				throw new \Techart\Core\ReadOnlyObjectException($property);
			default:
				return parent::__set($property, $value);
		}
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
			case 'files':
				return true;
			default:
				return parent::__isset($property);
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
		if ($this->__isset($property)) {
			throw new \Techart\Core\UndestroyablePropertyException($property);
		} else {
			parent::__unset($property);
		}
	}

	/**
	 * Возвращает итератор по содержимому каталога.
	 *
	 * @return \Techart\IO\FS\DirIterator
	 */
	public function getIterator()
	{
		return $this->make_default_iterator();
	}

	/**
	 * Возвращает значение индексированного свойства
	 *
	 * @param mixed $index
	 *
	 * @return \Techart\IO\FS\Dir|\Techart\IO\FS\File|null
	 */
	public function offsetGet($index)
	{
		return \Techart\IO\FS::file_object_for("{$this->path}/$index");
	}

	/**
	 * Устанавливает значение индексированного свойства
	 *
	 * @param string $index
	 * @param mixed  $value
	 *
	 * @throws \Techart\Core\MissingIndexedPropertyException
	 * @throws \Techart\Core\ReadOnlyIndexedPropertyException
	 */
	public function offsetSet($index, $value)
	{
		if ($this->offsetExists($index)) {
			throw new \Techart\Core\ReadOnlyIndexedPropertyException($index);
		}
		throw new \Techart\Core\MissingIndexedPropertyException($index);
	}

	/**
	 * Проверяет существование индексированного свойства
	 *
	 * @param string $index
	 *
	 * @return boolean
	 */
	public function offsetExists($index)
	{
		return \Techart\IO\FS::exists("{$this->path}/$index");
	}

	/**
	 * Удаляет индексированное свойство
	 *
	 * @param string $index
	 *
	 * @throws \Techart\Core\MissingIndexedPropertyException
	 * @throws \Techart\Core\ReadOnlyIndexedPropertyException
	 */
	public function offsetUnset($index)
	{
		if (isset($this[$index])) {
			throw new \Techart\Core\ReadOnlyIndexedPropertyException($index);
		}
		throw new \Techart\Core\MissingIndexedPropertyException($index);
	}

	public function rm()
	{
		if (!$this->exists()) {
			return $this;
		}
		foreach ($this as $obj) {
			$obj->rm();
		}
		rmdir($this->path);
		return $this;
	}

	public function create()
	{
		return \Techart\IO\FS::mkdir($this->path, null, true);
	}

	public function copy_to($dest)
	{
		$dest = \Techart\IO\FS::Dir($dest);
		if (!$this->exists()) {
			return $dest;
		}
		$dest->rm()->create();
		foreach ($this as $obj) $obj->copy_to($dest->path . DIRECTORY_SEPARATOR . $obj->name);
		return $dest;
	}

	public function move_to($dest)
	{
		$dest = $this->copy_to($dest);
		$this->rm();
		return $dest;
	}

	/**
	 * Возвращает итератор по умолчанию
	 *
	 * @return \Techart\IO\FS\DirIterator
	 */
	public function make_default_iterator()
	{
		return \Techart\IO\FS::Query()->apply_to($this);
	}

}
