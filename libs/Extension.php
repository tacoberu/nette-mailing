<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Nette;
use Nette\DI\CompilerExtension;
use Tracy\Debugger;
use InvalidArgumentException;


/**
 * @author Martin Takáč <martin@takac.name>
 */
final class Extension extends CompilerExtension
{

	const CONFIG_LOG = 'log';
	const CONFIG_SEND = 'send';
	const CONFIG_BOTH = 'both';

	private $defaults = [
		'do' => self::CONFIG_LOG,
		'log_directory' => '%appDir%/../log/mails',
		'mail_images_base_path' => '%wwwDir%',
		'senders' => []
	];



	function loadConfiguration()
	{
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('mailLogger'))
			->setClass('Taco\Nette\Mailing\FileMailLogger')
			->setArguments([$config['log_directory']]);
	}



	function getConfig()
	{
		$config = $this->validateConfig($this->defaults, $this->config);

		$config['do'] = self::castDo(Nette\DI\Helpers::expand(
			$config['do'],
			$this->getContainerBuilder()->parameters
		));

		$config['log_directory'] = Nette\DI\Helpers::expand(
			$config['log_directory'],
			$this->getContainerBuilder()->parameters
		);

		$config['mail_images_base_path'] = Nette\DI\Helpers::expand(
			$config['mail_images_base_path'],
			$this->getContainerBuilder()->parameters
		);

		return $config;
	}



	private function castDo($val)
	{
		switch($val) {
			case self::CONFIG_LOG:
				return MailingService::CONFIG_LOG;
			case self::CONFIG_SEND:
				return MailingService::CONFIG_SEND;
			case self::CONFIG_BOTH:
				return MailingService::CONFIG_SEND | MailingService::CONFIG_LOG;
			default:
				throw new InvalidArgumentException("Unsupported `do' value: `$val'.");
		}
	}

}
