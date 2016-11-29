<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Nette\Mail\Message;
use Nette\Utils\Validators;
use Nette\Mail\IMailer;


/**
 * Služba zastřešující komplet odesílání emailů.
 * @author Martin Takáč <martin@takac.name>
 */
class MailingService
{

	const CONFIG_LOG  = 1;
	const CONFIG_SEND = 2;

	/**
	 * @var int Bit-mask.
	 */
	private $config = self::CONFIG_LOG;// | self::CONFIG_SEND;

	/**
	 * @var IMailer
	 */
	private $mailer;

	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @var MessageTemplateProvider
	 */
	private $provider;

	/**
	 * @var MessageBuilder
	 */
	private $builderFactory;


	/**
	 * @param MessageTemplateProvider $provider
	 */
	function __construct(IMailer $mailer, MessageTemplateProvider $provider, Logger $logger, BuilderFactory $builder)
	{
		$this->mailer = $mailer;
		$this->logger = $logger;
		$this->provider = $provider;
		$this->builderFactory = $builder;
	}



	/**
	 * @param string $code Name of content, whitch load from provider.
	 * @param string $from Email of sender.
	 * @param string $recipient Email of recipient.
	 * @param hashtable of string $values
	 * @return Message
	 */
	function send($code, $from, $recipient, array $values = [])
	{
		Validators::assert($code, 'string:1..');
		Validators::assert($from, 'string:1..');
		Validators::assert($recipient, 'string:1..');

		$template = $this->provider->load($code);
		$builder = $this->builderFactory->create($template);
		$mail = $builder->compose($from, $recipient, $values);

		if ($this->config & self::CONFIG_SEND && $this->mailer) {
			$this->mailer->send($mail);
		}

		if ($this->config & self::CONFIG_LOG && $this->logger) {
			$this->logger->log($code, $mail);
		}
	}

}
