<?php

namespace Techart\IO\FS;

class StatException extends Exception
{
	protected $object;

	/**
	 * Конструктор
	 *
	 * @param  $object
	 */
	public function __construct($object)
	{
		parent::__construct("Can't stat object" . ((string)($this->object = $object)));
	}

}
