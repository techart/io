<?php

namespace Techart\IO\Stream;

/**
 * Поток для временных файлов
 *
 */
class TemporaryStream extends ResourceStream
{

	/**
	 * Конструктор
	 *
	 */
	public function __construct()
	{
		parent::__construct(tmpfile());
	}

	/**
	 * Декструктор
	 *
	 */
	public function __destruct()
	{
		$this->close();
	}

}
