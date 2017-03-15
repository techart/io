<?php

namespace Techart\IO;


class FS
{
	static protected $options = array(
		'dir_mod' => 0775,
		'file_mod' => 0664,
		'dir_own' => false,
		'file_own' => false,
		'dir_grp' => false,
		'file_grp' => false,
	);

	/**
	 * Устанавливает опции модуля
	 *
	 * @param array $options
	 *
	 * @return mixed
	 */
	static public function options(array $options = array())
	{
		if (count($options)) {
			\Techart\Core\Arrays::update(self::$options, $options);
		}
		return self::$options;
	}

	/**
	 * Устанавливает/возвращает опцию модуля
	 *
	 * @param string $name
	 * @param        $value
	 *
	 * @return mixed
	 */
	static public function option($name, $value = null)
	{
		$prev = isset(self::$options[$name]) ? self::$options[$name] : null;
		if ($value !== null) {
			self::options(array($name => $value));
		}
		return $prev;
	}

	/**
	 * Создает объект класса IO.FS.File
	 *
	 * @param string $path
	 *
	 * @return \Techart\IO\FS\File
	 */
	static public function File($path)
	{
		return $path instanceof \Techart\IO\FS\File ? $path : new \Techart\IO\FS\File($path);
	}

	/**
	 * Создает объект класса IO.FS.FileStream
	 *
	 * @param string $path
	 * @param string $mode
	 *
	 * @return \Techart\IO\FS\FileStream
	 */
	static public function FileStream($path, $mode = \Techart\IO\Stream::DEFAULT_OPEN_MODE)
	{
		return new \Techart\IO\FS\FileStream($path, $mode);
	}

	/**
	 * Создает объект класса IO.FS.Stat
	 *
	 * @param  $object
	 *
	 * @return \Techart\IO\FS\Stat
	 */
	static public function Stat($object)
	{
		return new \Techart\IO\FS\Stat($object);
	}

	/**
	 * Создает объект класса IO.FS.Dir
	 *
	 * @param string|\Techart\IO\FS\Dir $path
	 *
	 * @return \Techart\IO\FS\Dir
	 */
	static public function Dir($path = '.')
	{
		return $path instanceof \Techart\IO\FS\Dir ? $path : new \Techart\IO\FS\Dir($path);
	}

	/**
	 * Создает объект  класса IO.FS.Path
	 *
	 * @param string $path
	 *
	 * @return \Techart\IO\FS\Path
	 */
	static public function Path($path)
	{
		return new \Techart\IO\FS\Path($path);
	}

	/**
	 * Создает объект класса IO.FS.Query
	 *
	 * @return \Techart\IO\FS\Query
	 */
	static public function Query()
	{
		return new \Techart\IO\FS\Query();
	}

	/**
	 * Возвращает объект классов IO.FS.Dir или IO.FS.File по заданному пути
	 *
	 * @param string|\Techart\IO\FS\FSObject $path
	 *
	 * @return \Techart\IO\FS\Dir|\Techart\IO\FS\File|null
	 */
	static public function file_object_for($path)
	{
		$path = (string)$path;
		if (!self::exists($path)) {
			return null;
		}
		return self::is_dir($path) ? self::Dir($path) : self::File($path);
	}

	/**
	 * Возвращает объект класса IO.FS.Dir, соответствующий текущему каталогу.
	 *
	 * @return \Techart\IO\FS\Dir
	 */
	static public function pwd()
	{
		return self::Dir(getcwd());
	}

	/**
	 * Переходит в указанный каталог и возвращает соответствующий объект класса IO.FS.Dir
	 *
	 * @param string $path
	 *
	 * @return \Techart\IO\FS\Dir|null
	 */
	static public function cd($path)
	{
		return chdir($path) ? self::Dir(getcwd()) : null;
	}

	/**
	 * Создает каталог и возвращает соответствующий объект класса IO.FS.Dir
	 *
	 * @param string   $path
	 * @param null|int $mode
	 * @param bool     $recursive
	 *
	 * @return \Techart\IO\FS\Dir|null
	 */
	static public function mkdir($path, $mode = null, $recursive = true)
	{
		$mode = self::get_permision_for(null, $mode, 'mod', 'dir');
		$old = umask(0);
		$rs = (self::exists((string)$path) || mkdir((string)$path, $mode, $recursive)) ? self::Dir($path) : null;
		umask($old);
		return $rs;
	}

	/**
	 * @param      $path
	 * @param      $value
	 * @param      $type
	 * @param      $obj
	 *
	 * @return mixed
	 */
	static protected function get_permision_for($path, $value, $type, $obj = null)
	{
		if (!is_null($value)) {
			return $value;
		}
		$object = !empty($path) ? (is_dir($path) ? 'dir' : 'file') : $obj;
		return self::option("{$object}_{$type}");
	}

	/**
	 * Изменяет права доступа к файлу
	 *
	 * @param string $file
	 * @param int    $mode
	 *
	 * @return boolean
	 */
	static public function chmod($file, $mode = null)
	{
		$mode = self::get_permision_for($file, $mode, 'mod');
		return $mode ? @chmod((string)$file, $mode) : false;
	}

	public static function chmod_recursive($path, $mode = null)
	{
		$object = self::file_object_for($path);
		if ($object->exists()) {
			$object->chmod($mode);
			if (self::is_dir($object->path)) {
				foreach (self::Query()->recursive(true, true)->apply_to($object) as $nested) {
					$nested->chmod($mode);
				}
			}
		}
	}

	/**
	 * Изменяет владельца файла
	 *
	 * @param      $file
	 * @param      $owner
	 *
	 * @return bool
	 */
	static public function chown($file, $owner = null)
	{
		$owner = self::get_permision_for($file, $owner, 'own');
		return $owner ? @chown((string)$file, $owner) : false;
	}

	/**
	 * Изменяет группу файла
	 *
	 * @param      $file
	 * @param      $group
	 *
	 * @return bool
	 */
	static function chgrp($file, $group = null)
	{
		$group = self::get_permision_for($file, $group, 'grp');
		return $group ? @chgrp((string)$file, $group) : false;
	}

	/**
	 * Удаляет файл
	 *
	 * @param string $path
	 *
	 * @return boolean
	 */
	static public function rm($path)
	{
		$obj = self::file_object_for($path);
		return $obj ? $obj->rm() : false;
	}

	/**
	 * Удаляет файл
	 *
	 * @param string $path
	 *
	 * @return boolean
	 */
	static public function clear_dir($path)
	{
		if (!self::is_dir($path)) {
			return false;
		}
		$dir = self::Dir($path);
		$rc = true;
		foreach ($dir as $resource) {
			$rc = $resource->rm() && $rc;
		}
		return $rc;
	}

	/**
	 * Создает вложенные каталоги
	 *
	 * @param string $path
	 * @param int    $mode
	 *
	 * @return boolean
	 */
	static public function make_nested_dir($path, $mode = null)
	{
		return self::mkdir($path, $mode, true);
	}

	/**
	 * Проверяет существование файла или каталога
	 *
	 * @param string $path
	 *
	 * @return boolean
	 */
	static public function exists($path)
	{
		return file_exists((string)$path);
	}

	/**
	 * Проверяет, является ли файловый объект с заданным путем каталогом
	 *
	 * @param string $path
	 *
	 * @return boolean
	 */
	static public function is_dir($path)
	{
		return is_dir((string)$path);
	}

	/**
	 * Перемещает файл
	 *
	 * @param string $from
	 * @param string $to
	 *
	 * @return boolean
	 */
	static public function mv($from, $to)
	{
		$obj = self::file_object_for($from);
		return $obj ? $obj->move_to($to) : false;
	}

	/**
	 * Копирует файл
	 *
	 * @param string $from
	 * @param string $to
	 *
	 * @return boolean
	 */
	static public function cp($from, $to)
	{
		$obj = self::file_object_for($from);
		return $obj ? $obj->copy_to($to) : false;
	}

}
