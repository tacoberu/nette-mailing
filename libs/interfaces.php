<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;


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
	 * @param string $from
	 * @param string $recipient
	 * @param hashtable of string $values
	 * @return Nette\Mail\Message
	 */
	function compose($from, $recipient, array $values = []);

}
