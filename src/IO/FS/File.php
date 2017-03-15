<?php

namespace Techart\IO\FS;

/**
 * Объектное представление файла
 *
 * Файл представляет собой файловый объект, для которого могут быть получены метаданные и
 * открыт файловый поток. Объект файла предоставляет также возможность получения информации
 * о MIME-типе, соответствующем файлу, с использованием модуля MIME.
 * Объект может работать как итератор, в этом случае соответствующий файл открывается на
 * чтение и для него создается соответствующий поток.
 *
 * @property  $stream       ассоциированный поток
 * @property  $size         размер файла
 * @property  $mime_type    MIME-тип файла в виде объекта
 * @property  $content_type MIME-тип файла в виде строки
 */
class File extends FSObject implements
	\Techart\Core\PropertyAccessInterface,
	\IteratorAggregate
{
	protected $mime_type;
	protected $stream;

	/**
	 * Деструктор
	 *
	 * TODO: Тут наверно __destruct всеже?
	 */
	public function __destroy()
	{
		$this->close();
		parent::__destroy();
	}

	public function create()
	{
		return $this->update('', null);
	}

	/**
	 * Создает поток класса IO.FS.FileStream, соответствующий файлу
	 *
	 * @param string $mode
	 *
	 * @return \Techart\IO\FS\FileStream
	 */
	public function open($mode = \Techart\IO\Stream::DEFAULT_OPEN_MODE)
	{
		return ($this->stream && $this->stream->id) ? $this->stream : $this->stream = new \Techart\IO\FS\FileStream($this->path, $mode);
	}

	/**
	 * Закрывает поток, ассоциированный с файловым объектом
	 *
	 * @return \Techart\IO\FS\File
	 */
	public function close()
	{
		$this->stream = null;
		return $this;
	}

	/**
	 * Возвращает все содержимое файла в виде строки
	 *
	 * @param null $use_include_path
	 * @param null $context
	 * @param int  $offset
	 * @param null $maxlen
	 *
	 * @return string
	 */
	public function load($use_include_path = null, $context = null, $offset = 0, $maxlen = null)
	{
		return file_get_contents($this->path, $use_include_path, $context, $offset, $maxlen ? $maxlen : $this->size);
	}

	/**
	 * Эффективно записывает блок данных или строку в начало файла
	 *
	 * @param string $data
	 * @param int    $flags
	 *
	 * @return mixed
	 */
	public function update($data, $flags = 0)
	{
		$res = file_put_contents($this->path, $data, (int)$flags);
		$this->set_permission();
		return $res;
	}

	/**
	 * Добавляет данные в конец файла
	 *
	 * @param string $data
	 * @param int    $flags
	 *
	 * @return mixed
	 */
	public function append($data, $flags = 0)
	{
		$res = file_put_contents($this->path, $data, FILE_APPEND | $flags);
		$this->set_permission();
		return $res;
	}

	/**
	 * Перемещает файл
	 *
	 * @param string $destination
	 *
	 * @return \Techart\IO\FS\File
	 */
	public function move_to($destination)
	{
		if ($this->stream && $this->stream->id) {
			return null;
		}

		if (rename($this->path, $destination = $this->fix_destination($destination))) {
			$this->path = $destination;
			$this->stat = null;
			return $this;
		} else {
			return null;
		}
	}

	/**
	 * Копирует файл
	 *
	 * @param string $destination
	 *
	 * @return \Techart\IO\FS\File
	 */
	public function copy_to($destination)
	{
		if ($this->stream && $this->stream->id) {
			return null;
		}
		$copied = copy($this->path, $destination = $this->fix_destination($destination)) ? \Techart\IO\FS::File($destination) : null;
		return $copied;
	}

	public function rm()
	{
		return unlink($this->path);
	}

	/**
	 * Создает итератор для файлового объекта
	 */
	public function getIterator()
	{
		return $this->open()->getIterator();
	}

	/**
	 * Возвращает значение свойства
	 *
	 * @param string $property
	 *
	 * @return mixed
	 */
	public function __get($property)
	{
		switch ($property) {
			case 'stream':
				return $this->stream;
			case 'size':
				return @filesize($this->path);
			case 'mime_type':
				return $this->get_mime_type();
			case 'content_type':
				return $this->get_mime_type()->type;
			default:
				return parent::__get($property);
		}
	}

	/**
	 * Устанавливает значение свойства
	 *
	 * @param string $property
	 * @param        $value
	 *
	 * @return mixed
	 * @throws \Techart\Core\MissingPropertyException
	 * @throws \Techart\Core\ReadOnlyPropertyException
	 */
	public function __set($property, $value)
	{
		switch ($property) {
			case 'stream':
			case 'size':
			case 'mime_type':
			case 'content_type':
				throw new \Techart\Core\ReadOnlyPropertyException($property);
			default:
				return parent::__set($property, $value);
		}
	}

	/**
	 * Проверяет установку свойства
	 *
	 * @param string $property
	 *
	 * @return boolean
	 */
	public function __isset($property)
	{
		switch ($property) {
			case 'stream':
				return isset($this->stream);
			case 'size':
			case 'mime_type':
			case 'content_type':
				return true;
			default:
				return parent::__isset($property);
		}
	}

	/**
	 * Удаляет свойство
	 *
	 * @param string $property
	 *
	 * @throws \Techart\Core\MissingPropertyException
	 * @throws \Techart\Core\UndestroyablePropertyException
	 */
	public function __unset($property)
	{
		if ($this->__isset($property)) {
			throw new \Techart\Core\UndestroyablePropertyException($property);
		} else {
			parent::__unset($property);
		}
	}

	/**
	 * Возвращает MIME-тип, соответствующий файлу, в виде объекта класса MIME.Type
	 *
	 * @return \Techart\MIME\Type
	 */
	private function get_mime_type()
	{
		return $this->mime_type ? $this->mime_type : $this->mime_type = \Techart\MIME::type_for_file($this);
	}

	/**
	 * Корректирует новый путь к файлу для операций копирования и перемещения
	 *
	 * @param $destination
	 *
	 * @return string
	 */
	private function fix_destination($destination)
	{
		if (is_dir($destination)) {
			return (\Techart\Core\Strings::ends_with($destination, '/') ? $destination : $destination . '/') . @basename($this->path);
		}
		return $destination;
	}

}
