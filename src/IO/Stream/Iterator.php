<?php

namespace Techart\IO\Stream;

/**
 * Итератор потока
 *
 * <p>Позволяет использовать объект потока в качестве итератора, например, внутри цикла
 * foreach. В большинстве случаев объекты этого класса используются неявно через интерфейс
 * IteratorAggregate потока.</p>
 * <p>Чтение данных зависит от типа потока: для текстовых оно производится построчно, для
 * бинарных -- порциями размера, соответствующего размеру буфера чтения. Ключи итератора
 * соответствуют порядковому номеру операции чтения, начиная с 1.</p>
 *
 * @package IO\Stream
 */
class Iterator implements \Iterator
{

	private $stream;

	private $data = null;
	private $data_count = 0;

	/**
	 * Конструктор
	 *
	 * @param \Techart\IO\Stream\AbstractStream $stream
	 */
	public function __construct(AbstractStream $stream)
	{
		$this->stream = $stream;
	}

	/**
	 * Возвращает очередной элемент
	 *
	 * @return string
	 */
	public function current()
	{
		return $this->data === null ? $this->read() : $this->data;
	}

	/**
	 * Возвращает ключ для очередного элемента
	 *
	 * @return int
	 */
	public function key()
	{
		return $this->data_count;
	}

	/**
	 * Возвращает следующий элемент.
	 *
	 */
	public function next()
	{
		$this->read();
	}

	/**
	 * Сбрасывает итератор
	 *
	 */
	public function rewind()
	{
		$this->data_count = 0;
		$this->stream->rewind();
	}

	/**
	 * Проверяет доступность элементов итератора
	 *
	 * @return boolean
	 */
	public function valid()
	{
		return $this->data_count == 0 ? !$this->stream->eof() : !$this->data === false;
	}

	/**
	 * Выполняет чтение очередной порции данных из потока
	 *
	 * @return string
	 */
	private function read()
	{
		if (($this->data = $this->stream->read()) !== false) {
			$this->data_count++;
		}
		return $this->data;
	}

}

