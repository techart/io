<?php

namespace Techart;

/**
 * IO
 *
 * Базовый модуль ввода/вывода
 *
 * <p>Базовый модуль иерархии модулей IO, имеющих отношение к вводу/выводу.</p>
 * <p>Модуль определяет набор стандартных потоков ввода/вывода (stdin, stdout, stderr),
 * а также базовый класс исключений модулей IO.*. Реальная функциональность реализована в
 * других модулях IO.*.</p>
 * <p>В процессе загрузки модуль неявно подгружает модуль IO.Stream, таким образом, нет
 * необходимости подгружать IO.Stream явно.</p>
 *
 */

/**
 * Класс модуля
 *
 * <p>Класс модуля выполняет подгрузку модуля IO.Stream и определяет набор статических методов,
 * позволяющих получить доступ к стандартным потокам ввода/вывода.</p>
 *
 * @package IO
 */
class IO
{

	static protected $stdin;
	static protected $stdout;
	static protected $stderr;

	/**
	 * Возвращает объект класса IO.Stream.ResourceStream, соответствующий stdin.
	 *
	 * @return \Techart\IO\Stream\ResourceStream
	 */
	static public function stdin($stdin = null)
	{
		if ($stdin instanceof \Techart\IO\Stream\AbstractStream) {
			self::$stdin = $stdin;
		}
		return self::$stdin ? self::$stdin : self::$stdin = \Techart\IO\Stream::ResourceStream(STDIN);
	}

	/**
	 * Возвращает объект класса IO.Stream.ResourceStream, соответствующий stdout.
	 *
	 * @return \Techart\IO\Stream\ResourceStream
	 */
	static public function stdout($stdout = null)
	{
		if ($stdout instanceof \Techart\IO\Stream\AbstractStream) {
			self::$stdout = $stdout;
		}
		return self::$stdout ? self::$stdout : self::$stdout = \Techart\IO\Stream::ResourceStream(STDOUT);
	}

	/**
	 * Возвращает объект класса IO.Stream.ResourceStream, соответствующий stderr.
	 *
	 * @return \Techart\IO\Stream\ResourceStream
	 */
	static public function stderr($stderr = null)
	{
		if ($stderr instanceof \Techart\IO\Stream\AbstractStream) {
			self::$stderr = $stderr;
		}
		return self::$stderr ? self::$stderr : self::$stderr = \Techart\IO\Stream::ResourceStream(STDERR);
	}

}
