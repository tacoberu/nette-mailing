<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Latte;
use Nette\Application\LinkGenerator;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Mail\Message;
use PHPUnit_Framework_TestCase;


/**
 * @author Martin Takáč <martin@takac.name>
 */
class LatteMessageBuilderTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var LinkGenerator
	 */
	private $linkGenerator;

	/**
	 * @var ILatteFactory
	 */
	private $latteFactory;


	protected function setUp()
	{
		$this->linkGenerator = $this->getMockBuilder(LinkGenerator::class)
			->disableOriginalConstructor()
			->getMock();
		$this->latteFactory = $this->getMockBuilder(ILatteFactory::class)
			->setMethods(['create'])
			->getMock();
	}



	function testCheckProps()
	{
		$latte = $this->getMockBuilder(Latte\Engine::class)
			->setMethods(['setLoader', 'renderToString'])
			->getMock();
		$latte->expects($this->any())
			->method("setLoader");
		$latte->expects($this->at(1))
			->method("renderToString")
			->will($this->returnValue('a'));
		$latte->expects($this->at(2))
			->method("renderToString")
			->will($this->returnValue('b'));
		$latte->expects($this->at(3))
			->method("renderToString")
			->will($this->returnValue('c'));

		$this->latteFactory->expects($this->any())
			->method("create")
			->will($this->returnValue($latte));

		$msg = new Message;
		$msg->setFrom('a@dom.cz');
		$content = new MailContent('a', 'b', 'c');
		$builder = new LatteMessageBuilder($this->linkGenerator, $this->latteFactory);

		$mail = $builder->compose($msg, 'b@dom.cz', $content);
		$this->assertEquals('b', $mail->body);
		$this->assertEquals('c', $mail->htmlBody);
		$this->assertEquals('a', $mail->getHeader('Subject'));
		$this->assertEquals(['a@dom.cz' => NULL], $mail->getHeader('From'));
		$this->assertEquals(['b@dom.cz' => NULL], $mail->getHeader('To'));
	}

}
