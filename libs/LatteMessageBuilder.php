<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Latte\Loaders\StringLoader;
use Nette\Mail\Message,
	Nette\Utils\Validators;
use Nette\Templates\LatteFilter;


/**
 * Generování mailu pomocí Latte šablony.
 * @author Martin Takáč <martin@takac.name>
 */
class LatteMessageBuilder implements MessageBuilder
{

	/**
	 * @var ?
	 */
	private $translator;

	/**
	 * @var StringTemplate
	 */
	private $template;

	/**
	 * @var array
	 */
	private $defaults = [];


	function __construct($translator, $linkBuilder)
	{
		$this->translator = $translator;
		$this->setOption('control', $linkBuilder);
		$this->setOption('presenter', $linkBuilder);
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
	 * @return StringTemplate
	 */
	function getTemplate()
	{
		if (empty($this->template)) {
			$this->template = $this->createTemplate();
		}
		return $this->template;
	}



	/**
	 * @param Message $mail
	 * @param MailContent $content
	 * @param hashtable of string $values
	 * @return Message
	 */
	function compose(Message $mail, MailContent $content, array $values = [])
	{
		$args = array_merge($this->defaults, $values);

		$mail->setSubject($this->renderToString($content->getSubject(), $args));
		if ($content->getBody()) {
			$mail->setBody($this->renderToString($content->getBody(), $args));
		}

		if ($content->getHtml()) {
			$mail->setHtmlBody($this->renderToString($content->getHtml(), $args));
		}

		return $mail;
	}



	/**
	 * @param string
	 * @return string
	 */
	private function renderToString($content, array $args = [])
	{
		$template = $this->getTemplate()
			->setContent($content);
		foreach ($args as $key => $val) {
			$template->$key = $val;
		}
		return (string) $template;
	}



	/**
	 * @param string
	 * @return StringTemplate
	 */
	private function createTemplate()
	{
		$template = new StringTemplate;
		$template->registerFilter(new LatteFilter);
		$template->registerHelper('escape', 'Nette\Templates\TemplateHelpers::escapeHtml');
		$template->registerHelper('escapeUrl', 'rawurlencode');
		$template->registerHelper('stripTags', 'strip_tags');
		$template->registerHelper('nl2br', 'nl2br');
		$template->registerHelper('substr', 'iconv_substr');
		$template->registerHelper('repeat', 'str_repeat');
		$template->registerHelper('implode', 'implode');
		$template->registerHelper('number', 'number_format');
		$template->registerHelperLoader('Nette\Templates\TemplateHelpers::loader');

		return $template;
	}

}
