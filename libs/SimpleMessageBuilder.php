<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Nette\Mail\Message;
use Nette\Utils\Validators;


/**
 * Obsahuje jednoduché nahrazování zástupných znaků pomocí značek: %{jmeno-proměnné}.
 * @author Martin Takáč <martin@takac.name>
 */
class SimpleMessageBuilder implements MessageBuilder
{

	/**
	 * @param string $from Email of sender.
	 * @param string $recipient Email of recipient.
	 * @param MailContent $content
	 * @param hashtable of string $values
	 * @return Message
	 */
	function compose($from, $recipient, MailContent $content, array $values = [])
	{
		Validators::assert($from, 'email');
		Validators::assert($recipient, 'email');

		$args = [];
		foreach ($values as $k => $v) {
			$args['%{' . $k . '}'] = $v;
		}

		$mail = new Message;
		$mail->setFrom($from)
			->setSubject(strtr($content->getSubject(), $args));
		if ($content->getBody()) {
			$mail->setBody(strtr($content->getBody(), $args));
		}
		if ($content->getHtml()) {
			$mail->setHtmlBody(strtr($content->getHtml(), $args));
		}
		$mail->addTo($recipient);
		return $mail;
	}

}
