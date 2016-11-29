<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Utils\Validators;


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
	private $builder;

	/**
	 * @var string
	 */
	private $sender;


	/**
	 * @param IMailer $mailer Service for really sending of mail.
	 * @param MessageTemplateProvider $provider
	 * @param Logger $logger We are listening of sending. Optionaly persist every mail to file.
	 * @param MessageBuilder $builder Making mail from template. Template is plain text, or latte template, etc.
	 * @param string $sender Default email of sender.
	 */
	function __construct(IMailer $mailer, MessageTemplateProvider $provider, Logger $logger, MessageBuilder $builder, $sender = NULL)
	{
		Validators::assert($sender, 'string:1..|null');
		$this->mailer = $mailer;
		$this->logger = $logger;
		$this->provider = $provider;
		$this->builder = $builder;
		$this->sender = $sender;
	}



	/**
	 * @param string $code Name of content, whitch load from provider.
	 * @param string $recipient Email of recipient.
	 * @param hashtable of string $values
	 * @param string $sender Email of sender.
	 * @return Message
	 */
	function send($code, $recipient, array $values = [], $sender = NULL)
	{
		Validators::assert($code, 'string:1..');
		Validators::assert($sender, 'string:1..|null');
		Validators::assert($recipient, 'string:1..');
		$sender = $sender ?: $this->sender;

		$mail = $this->builder->compose($sender, $recipient, $this->provider->load($code), $values);

		if ($this->config & self::CONFIG_SEND && $this->mailer) {
			$this->mailer->send($mail);
		}

		if ($this->config & self::CONFIG_LOG && $this->logger) {
			$this->logger->log($code, $mail);
		}
	}

}
