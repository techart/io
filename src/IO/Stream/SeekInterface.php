<?php

namespace Techart\IO\Stream;

/**
 * Интерфейс позиционирования в потоке
 *
 * <p>Интерфейс должен быть реализован классами потоков, допускающих позиционирование,
 * например, файловыми потоками.</p>
 * <p>Интерфейс определяет набор констант, задающих тип смещения:</p>
 * SEEK_SET
 * абсолютное позицинировние;
 * SEEK_CUR
 * позиционирование относительно текущего положения;
 * SEEK_END
 * позиционирование относительно конца файла.
 *
 */
interface SeekInterface
{

	const SEEK_SET = 0;
	const SEEK_CUR = 1;
	const SEEK_END = 2;

	/**
	 * Устанавливает текущую позицию в потоке
	 *
	 * @param int $offset
	 * @param int $whence
	 *
	 * @return number
	 */
	public function seek($offset, $whence);

	/**
	 * Возвращает текущую позицию в потоке
	 *
	 * @return number
	 */
	public function tell();

}
