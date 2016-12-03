<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Nette\Mail\Message;
use PHPUnit_Framework_TestCase;


/**
 * @author Martin Takáč <martin@takac.name>
 */
class SimpleMessageBuilderTest extends PHPUnit_Framework_TestCase
{

	function testCheckProps()
	{
		$content = new MailContent('a', 'b', 'c');
		$builder = new SimpleMessageBuilder;
		$msg = new Message;
		$msg->setFrom('a@dom.cz');
		$mail = $builder->compose($msg, 'b@dom.cz', $content);
		$this->assertEquals('b', $mail->body);
		$this->assertEquals('c', $mail->htmlBody);
		$this->assertEquals('a', $mail->getHeader('Subject'));
		$this->assertEquals(['a@dom.cz' => NULL], $mail->getHeader('From'));
		$this->assertEquals(['b@dom.cz' => NULL], $mail->getHeader('To'));
	}



	function testReplaceVars()
	{
		$content = new MailContent('a from: %{from}', 'body %{foo} d efg %{foo}', 'html %{foo} d efg %{foo}');
		$msg = new Message;
		$msg->setFrom('a@dom.cz');
		$builder = new SimpleMessageBuilder;
		$mail = $builder->compose($msg, 'b@dom.cz', $content, ['foo' => 'Lorem ipsum doler ist.', 'from' => 'i']);
		$this->assertEquals('body Lorem ipsum doler ist. d efg Lorem ipsum doler ist.', $mail->body);
		$this->assertEquals('html Lorem ipsum doler ist. d efg Lorem ipsum doler ist.', $mail->htmlBody);
		$this->assertEquals('a from: i', $mail->getHeader('Subject'));
	}

}
