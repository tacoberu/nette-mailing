<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Nette\Mail\Message;
use Nette\Utils\Validators;
use RuntimeException;


/**
 * Poskytuje šablony emailů.
 * @author Martin Takáč <martin@takac.name>
 */
class FileBaseMessageTemplateProvider implements MessageTemplateProvider
{

	/**
	 * Zdrojové soubory obsahují HTML content. Plain se bude generovat.
	 */
	const TYPE_HTML = 'html';

	/**
	 * Zdrojové soubory obsahují plain content.
	 */
	const TYPE_PLAIN = 'plain';

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var enum self::TYPE_*
	 */
	private $srctype;

	/**
	 * Extension of filename like .txt, .latte, etc.
	 * @var string
	 */
	private $ext;


	/**
	 * @param string $path Cesta, kde se budou hledat šablony.
	 * @param string $ext Optional extension of file, because name of template not-corelated with filename.
	 */
	function __construct($path, $ext = NULL, $srctype = self::TYPE_HTML)
	{
		Validators::assert($path, 'string:1..');
		Validators::assert($ext, 'string:1..|null');
		$this->path = $path;
		$this->ext = $ext;
		$this->srctype = $srctype;
	}



	/**
	 * @param string $name Name of template.
	 * @return MailContent
	 */
	function load($name)
	{
		Validators::assert($name, 'string:1..');
		$paths = [
			$this->makePath(strtr($name, [':' => '/'])),
			$this->makePath(strtr($name, [':' => '/templates/'])),
		];

		foreach ($paths as $x) {
			if (file_exists($x)) {
				$path = $x;
				break;
			}
		}

		if ( ! isset($path)) {
			throw new RuntimeException("Template `{$name}' is not found ({$this->path}/{$name}).");
		}

		$content = file_get_contents($path);
		$pair = explode(PHP_EOL . PHP_EOL, $content, 2);
		if (count($pair) !== 2) {
			throw new RuntimeException('Template content is mistake. '
					. 'Must contain line with subject, two empty line, and next is content.');
		}

		if ( ! isset($pair[1]) || empty($pair[1])) {
			throw new RuntimeException("Template content is mistake. Content missing.");
		}

		if (strtolower(substr($pair[0], 0, 8)) !== 'subject:') {
			throw new RuntimeException("Template subject is mistake. Subject must starts word: `Subject:'.");
		}

		switch ($this->srctype) {
			case self::TYPE_PLAIN:
				return new MailContent(ltrim(substr($pair[0], 8)), $pair[1]);
			default:
				return new MailContent(ltrim(substr($pair[0], 8)), NULL, $pair[1]);
		}
	}



	/**
	 * @param string
	 * @return string
	 */
	private function makePath($name)
	{
		$path = $this->path . DIRECTORY_SEPARATOR . $name;
		if ($this->ext) {
			$path .= '.' . $this->ext;
		}
		return $path;
	}

}
