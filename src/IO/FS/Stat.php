<?php

namespace Techart\IO\FS;


/**
 * Объектное представление метаинформации об объекте файловой системы
 *
 * Объект предоставляет ту же информацию, что и встроенные функции fstat() и stat(), однако
 * делает это более удобным образом: при создании объекта может быть использован как путь
 * к файлу, так и файловый ресурс, все свойства, содержащие дату, возвращаются в виде
 * объектов класса Time.DateTime.
 *
 * @property               $nlink   количество ссылок
 * @property               $uid     userid владельца (В Windows это всегда будет 0)
 * @property               $gid     groupid владельца (В Windows это всегда будет 0)
 * @property               $size    размер в байтах
 * @property \DateTime $atime
 * @property \DateTime $mtime
 * @property \DateTime $ctime
 */
class Stat implements \Techart\Core\PropertyAccessInterface
{
    protected $stat = array();

    public function __construct($object)
    {
        if (!$stat = \Techart\Core\Types::is_resource($object) ? @fstat($object) : @stat((string)$object)) {
            throw new \Techart\IO\FS\StatException($object);
        }

        foreach ($stat as $k => $v) {
            switch ($k) {
                case 'atime':
                case 'mtime':
                case 'ctime':
                    $this->stat[$k] = \DateTime($v);
                    break;
                default:
                    $this->stat[$k] = $v;
            }
        }
    }

    /**
     * Возвращает значние свойства
     *
     * @param string $property
     *
     * @return mixed
     * @throws \Techart\Core\MissingPropertyException
     */
    public function __get($property)
    {
        if (isset($this->stat[$property])) {
            return $this->stat[$property];
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
        return isset($this->stat[$property]);
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
