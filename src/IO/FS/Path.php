<?php

namespace Techart\IO\FS;

/**
 * Объектное представление пути в файловой системе
 *
 * Объект представляет собой объектную обертку над встроенной функцией pathinfo().
 * Соответственно, он обеспечивает доступ к следующим свойствам:
 *
 * @property $dirname   путь к файлу
 * @property $basename  базовое имя файла
 * @property $extension расширение
 * @property $filename  имя файла
 */
class Path implements \Techart\Core\PropertyAccessInterface
{
	protected $info = array();

	/**
	 * Конструктор
	 *
	 * @param string $path
	 */
	public function __construct($path)
	{
		$this->info = @pathinfo((string)$path);
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
		if (isset($this->info[$property])) {
			return $this->info[$property];
		} elseif ($property == 'extension') {
			return '';
		} else {
			throw new \Techart\Core\MissingPropertyException($property);
		}
	}

	/**
	 * Устанавливает значение свойства
	 *
	 * @param string $property
	 * @param        $value
	 *
	 * @return mixed
	 * @throws \Techart\Core\ReadOnlyObjectException
	 */
	public function __set($property, $value)
	{
		throw new \Techart\Core\ReadOnlyObjectException($this);
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
		return isset($this->info[$property]);
	}

	/**
	 * Удаляет свойство
	 *
	 * @param string $property
	 *
	 * @throws \Techart\Core\ReadOnlyObjectException
	 */
	public function __unset($property)
	{
		throw new \Techart\Core\ReadOnlyObjectException($this);
	}

}
