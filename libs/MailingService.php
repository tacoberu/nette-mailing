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

	/**
	 * @var IMailer
	 */
	private $mailer;

	/**
	 * @var MessageTemplateProvider
	 */
	private $provider;


	/**
	 * @param MessageTemplateProvider $provider
	 */
	function __construct(IMailer $mailer, MessageTemplateProvider $provider)
	{
		$this->mailer = $mailer;
		$this->provider = $provider;
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
		$builder = new SimpleMessageBuilder($template);
		$mail = $builder->compose($from, $recipient, $values);

		$this->mailer->send($mail);
	}

}
