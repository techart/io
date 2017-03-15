<?php

namespace Techart\IO;

/**
 * Работа с потоками ввода/вывода
 *
 * Модуль обеспечивает минимальную объектную абстракцию потоков ввода/вывода, при этом
 * поток представляется в виде итерируемого объекта.<
 *
 */
 
class Stream
{
	const DEFAULT_OPEN_MODE = 'rb';
	const DEFAULT_CHUNK_SIZE = 8192;
	const DEFAULT_LINE_LENGTH = 1024;

	/**
	 * Создает объект класса IO.Stream.ResourceStream
	 *
	 * @param int $id
	 *
	 * @return \Techart\IO\Stream\ResourceStream
	 */
	static public function ResourceStream($id)
	{
		return new \Techart\IO\Stream\ResourceStream($id);
	}

	/**
	 * Создает объект класса IO.Stream.TemporaryStream
	 *
	 * @return \Techart\IO\Stream\TemporaryStream
	 */
	static public function TemporaryStream()
	{
		return new \Techart\IO\Stream\TemporaryStream();
	}

	/**
	 * Создает объект класса IO.Stream.NamedResourceStream
	 *
	 * @param string $uri
	 * @param string $mode
	 *
	 * @return \Techart\IO\Stream\ResourceStream
	 */
	static public function NamedResourceStream($uri, $mode = self::DEFAULT_OPEN_MODE)
	{
		return new \Techart\IO\Stream\NamedResourceStream($uri, $mode);
	}

}
