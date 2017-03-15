<?php

namespace Techart\IO\FS;

/**
 * Итератор по содержимому каталога
 *
 * Предназначен для использования вместе с объектами класса IO.FS.Dir.
 *
 */
class DirIterator implements \RecursiveIterator
{
	/** @var \Techart\IO\FS\Dir|\Techart\IO\FS\File|null|bool */
	protected $current = false;
	protected $query;
	protected $dir;
	protected $id;

	/**
	 * Конструктор
	 *
	 * @param \Techart\IO\FS\Dir   $dir
	 * @param \Techart\IO\FS\Query $query
	 */
	public function __construct(\Techart\IO\FS\Dir $dir, \Techart\IO\FS\Query $query = null)
	{
		$this->dir = $dir;
		$this->query = $query ? $query : \Techart\IO\FS::Query();
	}

	/**
	 * Проверяет, является ли текущий элемент подкаталогом
	 *
	 * @return boolean
	 */
	public function hasChildren()
	{
		return ($this->query->is_recursive() && ($this->current instanceof \Techart\IO\FS\Dir));
	}

	/**
	 * Возвращает итератор по подкаталогу
	 *
	 * @return \Techart\IO\FS\DirIterator
	 */
	public function getChildren()
	{
		return new \Techart\IO\FS\DirIterator($this->current, $this->query);
	}

	/**
	 * Возвращает текущий элемент
	 *
	 * @return \Techart\IO\FS\FSObject
	 */
	public function current()
	{
		return $this->current;
	}

	/**
	 * Возвращает ключ текущего элемента
	 *
	 * @return string
	 */
	public function key()
	{
		return $this->current->path;
	}

	/**
	 * Сбрасывает итератор
	 *
	 */
	public function rewind()
	{
		if ($this->id) {
			@closedir($this->id);
		}
		rewinddir($this->id = @opendir($this->dir->path));
		$this->skip_to_next();
	}

	/**
	 * Переходит к следующему элементу итератора
	 *
	 */
	public function next()
	{
		return $this->skip_to_next();
	}

	/**
	 * Проверяет существование текущего элемента итератора
	 *
	 * @return boolean
	 */
	public function valid()
	{
		return $this->current ? true : false;
	}

	/**
	 * Переходит к очередному элементу, удовлетворяющему условиям поиска
	 *
	 */
	protected function skip_to_next()
	{
		do {
			$name = readdir($this->id);
			$path = $this->dir->path . '/' . $name;
		} while ($name !== false && ($name == '.' || $name == '..' || $this->query->forbids($path = $this->dir->path . '/' . $name)));
		if ($name !== false) {
			$this->current = \Techart\IO\FS::file_object_for($path);
		} else {
			@closedir($this->id);
			$this->id = null;
			$this->current = null;
		}
		return $this->current;
	}
}
