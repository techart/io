<?php

namespace Techart\IO\Stream;

/**
 * Поток для именованных ресурсов
 *
 */
class NamedResourceStream extends ResourceStream
{

	/**
	 * Конструктор
	 *
	 * @param string $uri
	 * @param string $mode
	 *
	 * @throws \Techart\IO\Stream\Exception
	 */
	public function __construct($uri, $mode = \Techart\IO\Stream::DEFAULT_OPEN_MODE)
	{
		if (!$this->id = @fopen($uri, $mode)) {
			throw new Exception("Unable to open named resource: $uri");
		}
	}

	/**
	 * Деструктор
	 *
	 */
	public function __destruct()
	{
		$this->close();
	}

}
