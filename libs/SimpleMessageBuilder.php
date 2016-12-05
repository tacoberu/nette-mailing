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
	 * @param Message $mail
	 * @param MailContent $content
	 * @param hashtable of string $values
	 * @return Message
	 */
	function compose(Message $mail, MailContent $content, array $values = [])
	{
		$args = [];
		foreach ($values as $k => $v) {
			$args['%{' . $k . '}'] = $v;
		}

		$mail->setSubject(strtr($content->getSubject(), $args));

		if ($content->getBody()) {
			$mail->setBody(strtr($content->getBody(), $args));
		}

		if ($content->getHtml()) {
			$mail->setHtmlBody(strtr($content->getHtml(), $args));
		}

		return $mail;
	}

}
