<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Nette;
use Nette\Utils\Validators;


/**
 * Data mailu se skládají z předmětu, vlastního textu, a dalších věcí, které časem přibudou.
 * Adresát se dodá jinudy. Stejně tak jako proměnné hodnoty obsahu majlu.
 *
 * @author Martin Takáč <martin@takac.name>
 *
 * @property string $subject
 * @property string $body
 * @property string $html
 */
class MailContent
{

	use Nette\SmartObject;


	/**
	 * @var string
	 */
	private $subject;

	/**
	 * @var string
	 */
	private $body;

	/**
	 * @var string
	 */
	private $html;


	/**
	 * @param string $subject
	 * @param string $body
	 * @param string $html
	 */
	function __construct($subject, $body = NULL, $html = NULL)
	{
		Validators::assert($subject, 'string:1..');
		Validators::assert($body, 'string:1..|null');
		Validators::assert($html, 'string:1..|null');
		if (empty($body) && empty($html)) {
			throw new Nette\Utils\AssertionException('Must be body or html or both. Is any.');
		}
		$this->subject = $subject;
		$this->body = $body;
		$this->html = $html;
	}



	/**
	 * @return string
	 */
	function getSubject()
	{
		return $this->subject;
	}



	/**
	 * @return string
	 */
	function getBody()
	{
		return $this->body;
	}



	/**
	 * @return string
	 */
	function getHtml()
	{
		return $this->html;
	}

}
