<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Latte\Loaders\StringLoader;
use Nette\Mail\Message,
	Nette\Utils\Validators,
	Nette\Application\LinkGenerator,
	Nette\Bridges\ApplicationLatte\ILatteFactory,
	Nette\Bridges\ApplicationLatte\UIMacros;


/**
 * Generování mailu pomocí Latte šablony.
 * @author Martin Takáč <martin@takac.name>
 */
class LatteMessageBuilder implements MessageBuilder
{

	/**
	 * @var LinkGenerator
	 */
	private $linkGenerator;

	/**
	 * @var ILatteFactory
	 */
	private $latteFactory;

	/**
	 * @var array
	 */
	private $defaults = [];


	function __construct(LinkGenerator $generator, ILatteFactory $latteFactory)
	{
		$this->linkGenerator = $generator;
		$this->latteFactory = $latteFactory;
	}



	/**
	 * @param string $name Name of value, which replaced in template.
	 * @param mixin $value Value for template.
	 */
	function setOption($name, $value)
	{
		$this->defaults[$name] = $value;
		return $this;
	}



	/**
	 * @param Message $mail
	 * @param string $recipient Email of recipient.
	 * @param MailContent $content
	 * @param hashtable of string $values
	 * @return Message
	 */
	function compose(Message $mail, $recipient, MailContent $content, array $values = [])
	{
		Validators::assert($recipient, 'email');

		$args = array_merge($this->defaults, $values, ['_control' => $this->linkGenerator]);
		$latte = $this->getLatte();

		$mail->setSubject($latte->renderToString($content->getSubject(), $args));

		if ($content->getBody()) {
			$mail->setBody($latte->renderToString($content->getBody(), $args));
		}

		if ($content->getHtml()) {
			$mail->setHtmlBody($latte->renderToString($content->getHtml(), $args));
		}

		$mail->addTo($recipient);

		return $mail;
	}



	/**
	 * @return Latte\Engine
	 */
	private function getLatte()
	{
		$latte = $this->latteFactory->create();
		$latte->setLoader(new StringLoader);
		UIMacros::install($latte->getCompiler());

		return $latte;
	}

}
