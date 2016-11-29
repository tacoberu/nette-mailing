<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Nette\Mail\Message;


/**
 * Poskytuje šablony emailů.
 * @author Martin Takáč <martin@takac.name>
 */
interface MessageTemplateProvider
{

	/**
	 * @param string $name Name of template.
	 * @return MailContent
	 */
	function load($name);

}



/**
 * Pro konkrétní argumenty vytvoří mail.
 * @author Martin Takáč <martin@takac.name>
 */
interface MessageBuilder
{

	/**
	 * @param string $from Who is sending this mail.
	 * @param string $recipient Who is receiving is mail.
	 * @param MailContent $content
	 * @param hashtable of string $values
	 * @return Message
	 */
	function compose($from, $recipient, MailContent $content, array $values = []);

}



/**
 * We are listening send of mail.
 * @author Martin Takáč <martin@takac.name>
 */
interface Logger
{

	/**
	 * Save mail messages to eml file.
	 * @param string $name Name of message for information.
	 * @param Message $mail
	 */
	function log($name, Message $mail);

}
