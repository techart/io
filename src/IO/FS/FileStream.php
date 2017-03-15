<?php

namespace Techart\IO\FS;

/**
 * Файловый поток
 *
 * Расширяет базовый класс IO.Stream.NamedResourceStream поддержкой интерфейса
 * IO.Stream.SeekableInterface и реализацией метода truncate().
 *
 */
class FileStream extends \Techart\IO\Stream\NamedResourceStream implements \Techart\IO\Stream\SeekInterface
{
	/**
	 * Устанавливает текущую позицию в потоке
	 *
	 * @param int $offset
	 * @param int $whence
	 *
	 * @return int
	 */
	public function seek($offset, $whence = SEEK_SET)
	{
		return @fseek($this->id, $offset, $whence);
	}

	/**
	 * Возвращает текущую позицию в потоке
	 *
	 * @return int
	 */
	public function tell()
	{
		return @ftell($this->id);
	}

	/**
	 * Обрезает файл до заданной длины
	 *
	 * @param int $size
	 *
	 * @return boolean
	 */
	public function truncate($size = 0)
	{
		return @ftruncate($this->id, $size);
	}

}
