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
 * Služba zastřešující komplet odesílání emailů. Zdroj obsahu je pojmenován a získán
 * pomocí MessageTemplateProvider. Obsah je následně sestaven pomocí MessageBuilder.
 * Email se volitelně může zalogovat pomocí Logger, a odeslat pomocí IMailer.
 *
 * K dispozici je dvojice metod. Methoda send() slouží jako zkratka pro nejpohodlnější
 * odeslání emailu. Methoda sendMessage() zase umožňuje komplexnější vymazlení odesílaného
 * Message objektu. V tomto případě methoda slouží hlavně pro sestavení obsahu (plainBody i htmlBody)
 * a jejího zalogování či odeslání.
 *
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
	function __construct(IMailer $mailer, MessageTemplateProvider $provider, Logger $logger, MessageBuilder $builder, $sender = NULL, $config = NULL)
	{
		Validators::assert($sender, 'string:1..|null');
		$this->mailer = $mailer;
		$this->logger = $logger;
		$this->provider = $provider;
		$this->builder = $builder;
		$this->sender = $sender;
		if ($config) {
			$this->config = $config;
		}
	}



	/**
	 * Shortcut for easy sending of mail.
	 *
	 * @param string $code Name of content, whitch load from provider.
	 * @param hashtable of string $values Nahrazovaný obsah v šabloně.
	 * @param array of string $recipients Email of recipient.
	 * @param string $sender Email of sender.
	 *
	 * @return bool
	 */
	function send($code, array $values, array $recipients, $sender = NULL)
	{
		Validators::assert($code, 'string:1..');
		Validators::assert($sender, 'string:1..|null');
		$sender = $sender ?: $this->sender;

		$mail = new Message;
		$mail->setFrom($sender);

		return $this->sendMessage($mail, $code, $values, $recipients);
	}



	/**
	 * Send complexity setting of mail.
	 *
	 * @param Message $mail Instance s nastavenýma specielníma hodnotama.
	 * @param string $code Name of content, whitch load from provider.
	 * @param hashtable of string $values Nahrazovaný obsah v šabloně.
	 * @param array of string $recipients Email of recipient.
	 *
	 * @return bool
	 */
	function sendMessage(Message $mail, $code, array $values, array $recipients)
	{
		Validators::assert($code, 'string:1..');

		if (empty($mail->getFrom())) {
			$mail->setFrom($this->sender);
		}

		foreach ($recipients as $recipient) {
			Validators::assert($recipient, 'string:1..');
			$mail->addTo($recipient);
		}

		$mail = $this->builder->compose($mail, $this->provider->load($code), $values);

		if ($this->config & self::CONFIG_SEND && $this->mailer) {
			$this->mailer->send($mail);
		}

		if ($this->config & self::CONFIG_LOG && $this->logger) {
			$this->logger->log($code, $mail);
		}

		return TRUE;
	}

}
