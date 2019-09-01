<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Nette;
use Nette\Mail\Message;
use Nette\Utils\Validators;
use RuntimeException;


/**
 * Persist email content to path by year, month, and day.
 * @credits by Pavel Janda <me@paveljanda.com>
 * @author Martin Takáč <martin@takac.name>
 */
class FileMailLogger implements Logger
{

	use Nette\SmartObject;


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
		Validators::assert($path, 'string:1..');
		if ( ! file_exists($path)) {
			throw new RuntimeException("Path `$path' is not found.");
		}
		$this->logDest = $path;
	}



	/**
	 * Log mail messages to eml file.
	 * @param string $name Name of email, like 'contact' or 'Catalog:contact'.
	 * @param Message $mail
	 */
	function log($name, Message $mail)
	{
		Validators::assert($name, 'string:1..');
		$name .= '.' . time();
		$file = $this->requireLogFile($name);

		if (file_exists($file) && filesize($file)) {
			$file = str_replace(static::LOG_EXTENSION, '.' . uniqid() . static::LOG_EXTENSION, $file);
		}

		file_put_contents($file, $mail->generateMessage());
	}



	/**
	 * If not already created, creat edirectory path that stickes to standard described above.
	 * @param string $type
	 * @param string $timestamp
	 * @return string
	 */
	private function requireLogFile($type)
	{
		$file = implode(DIRECTORY_SEPARATOR, [
			$this->logDest,
			date('Y'),
			date('Y-m'),
			date('Y-m-d'),
			self::saniteFilename($type /*. '?=>!:'*/ . '.' . static::LOG_EXTENSION)
		]);

		if ( ! file_exists(dirname($file))) {
			mkdir(dirname($file), 0777, TRUE);
		}

		if ( ! file_exists($file)) {
			touch($file);
		}

		return $file;
	}



	/**
	 * @param string
	 * @return string
	 */
	private static function saniteFilename($name)
	{
		return strtr($name, ':?!=>', '_____');
	}

}
