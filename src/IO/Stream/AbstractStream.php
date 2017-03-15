<?php

namespace Techart\IO\Stream;

/**
 * Базовый класс потока
 *
 * Определяет интерфейс класса потока, предназначен для использования в качестве базового
 * класс при реализации специфичных классов потоков.
 *
 */
abstract class AbstractStream implements \IteratorAggregate
{
	protected $binary = false;

	/**
	 * Читает данные из потока
	 *
	 * @param int $length
	 *
	 * @return string
	 */
	public function read($length = null)
	{
		return $this->binary ?
			$this->read_chunk($length) :
			$this->read_line($length);
	}

	/**
	 * Читает блок данных из бинарного потока.
	 *
	 * @abstract
	 *
	 * @param int $length
	 *
	 * @return string
	 */
	abstract public function read_chunk($length = null);

	/**
	 * Читает строку из текстового потока
	 *
	 * @abstract
	 *
	 * @param int $length
	 *
	 * @return string
	 */
	abstract public function read_line($length = null);

	/**
	 * Записывает данные в поток
	 *
	 * @abstract
	 *
	 * @param string $data
	 *
	 * @return \Techart\IO\Stream\AbstractStream
	 */
	abstract public function write($data);

	/**
	 * Записывает данные в поток, используя форматирование в стиле printf
	 *
	 * @return \Techart\IO\Stream\AbstractStream
	 */
	public function format()
	{
		$args = func_get_args();
		return $this->write(vsprintf(array_shift($args), $args));
	}

	/**
	 * Закрывает поток
	 *
	 */
	public function close()
	{
	}

	/**
	 * Устанвливает позицию в начало
	 *
	 */
	public function rewind()
	{
		return $this;
	}

	/**
	 * Переводит поток в бинарный режим
	 *
	 * @param boolean $is_binary
	 *
	 * @return $this
	 */
	public function binary($is_binary = true)
	{
		$this->binary = $is_binary;
		return $this;
	}

	/**
	 * Переводит поток в текстовый режим
	 *
	 * @param boolean $is_text
	 *
	 * @return $this
	 */
	public function text($is_text = true)
	{
		$this->binary = !$is_text;
		return $this;
	}

	/**
	 * Проверяет, достигнут ли конец потока
	 *
	 * @abstract
	 * @return boolean
	 */
	abstract public function eof();
}
