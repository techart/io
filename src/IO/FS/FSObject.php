<?php

namespace Techart\IO\FS;

/**
 * Базовый класс объектов файловой системы
 *
 * Предназначен для использования в качестве базового класса для более специфичных классов,
 * представляющих файлы и каталоги. Каждый объект класса характеризуется своим именем и
 * метаданными, содержащимися в виде объекта IO.FS.Stat, загружаемого по требованию.
 * Класс также реализует набор операций, в равной степени применимых к файлам и каталогам.
 *
 * @property string $path      путь к файлу
 * @property string $dir_name  имя каталога
 * @property string $name      имя файла
 * @property string $real_path реальный путь к файлу (с раскрытыми ., .., и т.д.)
 * @property string $stat      метаданные в виде объекта класса IO.FS.Stat
 */
class FSObject implements
    \Techart\Core\StringifyInterface,
    \Techart\Core\EqualityInterface
{
    protected $path;
    protected $stat;

    /**
     * Конструктор
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Изменяет права доступа к файловому объекту
     *
     * @param int $mode
     *
     * @return boolean
     */
    public function chmod($mode = null)
    {
        $this->stat = null;
        return \Techart\IO\FS::chmod($this->path, $mode);
    }

    /**
     * Изменяет владельца файлового объекта
     *
     * @param  $owner
     *
     * @return boolean
     */
    public function chown($owner = null)
    {
        $this->stat = null;
        return \Techart\IO\FS::chown($this->path, $owner);
    }

    public function chgrp($group = null)
    {
        $this->stat = null;
        return \Techart\IO\FS::chgrp($this->path, $group);
    }

    public function set_permission($mode = null, $owner = null, $group = null)
    {
        $this->chmod($mode);
        $this->chown($owner);
        $this->chgrp($group);
        return $this;
    }

    public function exists()
    {
        return file_exists($this->path);
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
            case 'path':
                return $this->path;
            case 'dir_name':
            case 'dirname':
                return @dirname($this->path);
            case 'name':
                return @basename($this->path);
            case 'real_path':
                return @realpath($this->path);
            case 'stat':
                if (!$this->stat) {
                    $this->stat = \Techart\IO\FS::Stat($this->path);
                }
                return $this->stat;
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
     * @return mixed
     * @throws \Techart\Core\MissingPropertyException
     * @throws \Techart\Core\ReadOnlyPropertyException
     */
    public function __set($property, $value)
    {
        switch ($property) {
            case 'path':
            case 'dir_name':
            case 'name':
            case 'real_path':
            case 'stat':
                throw new \Techart\Core\ReadOnlyPropertyException($property);
            default:
                throw new \Techart\Core\MissingPropertyException($property);
        }
    }

    /**
     * Проверяет установку свойства объекта
     *
     * @param string $property
     *
     * @return boolean
     */
    public function __isset($property)
    {
        switch ($property) {
            case 'path':
            case 'dir_name':
            case 'name':
            case 'real_path':
            case 'stat':
                return true;
            default:
                return false;
        }
    }

    /**
     * Удаляет свойство объекта
     *
     * @param string $property
     *
     * @throws \Techart\Core\MissingPropertyException
     * @throws \Techart\Core\UndestroyablePropertyException
     */
    public function __unset($property)
    {
        switch ($property) {
            case 'path':
            case 'dir_name':
            case 'name':
            case 'real_path':
            case 'stat':
                throw new \Techart\Core\UndestroyablePropertyException($property);
            default:
                throw new \Techart\Core\MissingPropertyException($property);
        }
    }

    /**
     * @return string
     */
    public function as_string()
    {
        return $this->real_path;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->real_path;
    }

    /**
     * @param  $to
     *
     * @return boolean
     */
    public function equals($to)
    {
        return $to instanceof self && $this->real_path == $to->real_path;
    }

}
