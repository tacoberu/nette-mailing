<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Nette;
use Nette\Mail\Message;
use RuntimeException;


/**
 * Persist email content to path by year, month, and day.
 * @credits by Pavel Janda <me@paveljanda.com>
 * @author Martin Takáč <martin@takac.name>
 */
class FileMailLogger extends Nette\Object implements Logger
{

	const LOG_EXTENSION = 'eml';

	/**
	 * @var string
	 */
	private $logDest;


	/**
	 * @param string $path Name of path where find templates.
	 */
	function __construct($path)
	{
		if ( ! file_exists($path)) {
			throw new RuntimeException("Path `$path' is not found.");
		}
		$this->logDest = $path;
	}



	/**
	 * Log mail messages to eml file.
	 * @param string $name
	 * @param Message $mail
	 */
	function log($name, Message $mail)
	{
		$timestamp = date('Y-m-d H:i:s');
		$name .= '.' . time();
		$file = $this->getLogFile($name, $timestamp);

		if (file_exists($file) && filesize($file)) {
			$file = str_replace(static::LOG_EXTENSION, '.' . uniqid() . static::LOG_EXTENSION, $file);
		}

		file_put_contents($file, $mail->generateMessage());
	}



	/**
	 * If not already created, creat edirectory path that stickes to standard described above
	 * @param string $type
	 * @param string $timestamp
	 * @return string
	 */
	private function getLogFile($type, $timestamp)
	{
		preg_match('/^((([0-9]{4})-[0-9]{2})-[0-9]{2}).*/', $timestamp, $fragments);

		$year = $this->logDest . '/' . $fragments[3];
		$month = $year . '/' . $fragments[2];
		$day = $month . '/' . $fragments[1];
		$file = $day . '/' . $type . '.' . static::LOG_EXTENSION;

		if ( ! file_exists($day)) {
			mkdir($day, 0777, TRUE);
		}

		if ( ! file_exists($file)) {
			touch($file);
		}

		return $file;
	}

}
