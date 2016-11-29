<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Nette\Mail\Message;
use Nette\Utils\Strings;
use PHPUnit_Framework_TestCase;


/**
 * @author Martin Takáč <martin@takac.name>
 */
class MailContentTest extends PHPUnit_Framework_TestCase
{

	function testSubjectMustBe()
	{
		$this->setExpectedException('Nette\Utils\AssertionException', 'The variable expects to be string in range 1.., string \'\' given.');
		new MailContent('', '');
	}



	function testBodyMustBe()
	{
		$this->setExpectedException('Nette\Utils\AssertionException', 'The variable expects to be string in range 1.. or null, string \'\' given.');
		new MailContent('sbj', '');
	}



	function testBodyMayBeNull()
	{
		new MailContent('sbj', NULL, 'a');
	}



	function testHtmlMustBe()
	{
		$this->setExpectedException('Nette\Utils\AssertionException', 'The variable expects to be string in range 1.. or null, string \'\' given.');
		new MailContent('sbj', 'a', '');
	}



	function testHtmlMayBeNull()
	{
		new MailContent('sbj', 'a', NULL);
	}



	function testMustBeHtmlOrBody()
	{
		$this->setExpectedException('Nette\Utils\AssertionException', 'Must be body or html or both. Is any.');
		new MailContent('sbj', NULL, NULL);
	}



	function testOnlyBody()
	{
		$content = new MailContent('sbj', 'body');
		$this->assertEquals('sbj', $content->subject);
		$this->assertEquals('body', $content->body);
		$this->assertNull($content->html);
	}



	function testBothContent()
	{
		$content = new MailContent('sbj', 'body', '<p>body</p>');
		$this->assertEquals('sbj', $content->subject);
		$this->assertEquals('body', $content->body);
		$this->assertEquals('<p>body</p>', $content->html);
	}

}
