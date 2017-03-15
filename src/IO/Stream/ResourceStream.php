<?php

namespace Techart\IO\Stream;


/**
 * Поток, связанный с ресурсом
 *
 * Представляет поток, связанный с неким ресурсом ввода/вывода по его идентификатору.
 *
 */

class ResourceStream extends AbstractStream
{

	protected $id = false;

	/**
	 * Конструктор
	 *
	 * @param int $id
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}

	/**
	 * Записывает данные в поток
	 *
	 * @param  $data
	 *
	 * @return \Techart\IO\Stream\ResourceStream
	 */
	public function write($data)
	{
		fwrite($this->id, (string)$data);
		return $this;
	}

	/**
	 * Записывает в поток строку, добавляя в конец символ перевода строки
	 *
	 * @param  $data
	 *
	 * @return \Techart\IO\Stream\ResourceStream
	 */
	public function write_line($data)
	{
		$this->write($data . "\n");
		return $this;
	}

	/**
	 * @param  $data
	 *
	 * @return \Techart\IO\Stream\ResourceStream
	 */
	public function line($data)
	{
		return $this->write_line($data);
	}

	/**
	 * Читает данные из бинарного потока
	 *
	 * @param int $length
	 *
	 * @return string
	 */
	public function read_chunk($length = null)
	{
		return fread($this->id, $length ? (int)$length : \Techart\IO\Stream::DEFAULT_CHUNK_SIZE);
	}

	/**
	 * Читает строку из текстового потока
	 *
	 * @param int $length
	 *
	 * @return string
	 */
	public function read_line($length = null)
	{
		return (!$this->id || $this->eof()) ?
			null :
			fgets($this->id, $length ? (int)$length : \Techart\IO\Stream::DEFAULT_LINE_LENGTH);
	}

	/**
	 * Определяет, достигнут ли конец потока
	 *
	 * @return boolean
	 */
	public function eof()
	{
		return !$this->id || @feof($this->id);
	}

	/**
	 * Закрывает поток
	 *
	 */
	public function close()
	{
		if ($this->id) {
			if (@fclose($this->id)) {
				$this->id = null;
			} else {
				throw new Exception("Unable to close named resource: $this->id");
			}
		}
	}

	/**
	 * Устанвливает позицию в начало
	 *
	 */
	public function rewind()
	{
		@rewind($this->id);
		return $this;
	}

	/**
	 * Возвращает все содержимое потока в виде строки
	 *
	 * @return string
	 */
	public function load()
	{
		$this->rewind();
		return stream_get_contents($this->id);
	}

	/**
	 * Возвращает значение свойства
	 *
	 * @param string $property
	 *
	 * @return mixed
	 * @throws \Techart\Core\MissingPropertyException
	 */
	public function __get($property)
	{
		switch ($property) {
			case 'id':
			case 'binary':
				return $this->$property;
			default:
				throw new \Techart\Core\MissingPropertyException($property);
		}
	}

	/**
	 * Устанавливает значение свойства
	 *
	 * @param string $property
	 * @param        $value
	 *
	 * @return mixed|void
	 * @throws \Techart\Core\MissingPropertyException
	 * @throws \Techart\Core\ReadOnlyPropertyException
	 */
	public function __set($property, $value)
	{
		switch ($property) {
			case 'id':
			case 'binary':
				throw new \Techart\Core\ReadOnlyPropertyException($property);
			default:
				throw new \Techart\Core\MissingPropertyException($property);
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
			case 'id':
			case 'binary':
				return true;
			default:
				return false;
		}
	}

	/**
	 * Удаляет свойство
	 *
	 * @param string $property
	 *
	 * @throws \Techart\Core\MissingPropertyException
	 * @throws \Techart\Core\ReadOnlyPropertyException
	 */
	public function __unset($property)
	{
		switch ($property) {
			case 'id':
			case 'binary':
				throw new \Techart\Core\ReadOnlyPropertyException($property);
			default:
				throw new \Techart\Core\MissingPropertyException($property);
		}
	}

	/**
	 * Создает итератор потока класса IO.Stream.Iterator
	 *
	 */
	public function getIterator()
	{
		return new Iterator($this);
	}

}
