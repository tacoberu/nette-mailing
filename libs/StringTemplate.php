<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Nette;
use Nette\Loaders\LimitedScope;
use Nette\Templates\BaseTemplate;


/**
 * Šablona in-the-fly bez uložení do souboru.
 *
 * @author Martin Takáč <martin@takac.name>
 */
class StringTemplate extends BaseTemplate
{

	/**
	 * @var string
	 */
	private $content;


	/**
	 * @param string
	 */
	function setContent($content)
	{
		$this->content = (string) $content;
		return $this;
	}



	/**
	 * Renders template to output.
	 * @return void
	 */
	function render()
	{
		$this->__set('template', $this);

		if ( ! $this->getFilters()) {
			$this->onPrepareFilters($this);
		}

		$content = $this->compile($this->content);

		LimitedScope::evaluate($content, $this->getParams());
	}

}
