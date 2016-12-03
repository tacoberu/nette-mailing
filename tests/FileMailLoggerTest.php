<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use Nette\Mail\Message;
use Nette\Utils\Strings;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;


/**
 * @author Martin Takáč <martin@takac.name>
 */
class FileMailLoggerTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var org\bovigo\vfs\vfsStreamDirectory
	 */
	private $root;


	function setUp()
	{
		$this->root = vfsStream::setup('home', NULL, [
			'sm' => [],
		]);
	}



	function testConfigInvalidFilename()
	{
		$this->setExpectedException('Nette\Utils\AssertionException', 'The variable expects to be string in range 1.., string \'\' given.');
		new FileMailLogger('');
	}



	function testConfigFileNotFound()
	{
		$this->setExpectedException('RuntimeException', 'Path `none\' is not found.');
		new FileMailLogger('none');
	}



	function testCreate()
	{
		$logger = new FileMailLogger(vfsStream::url('home/sm'));

		$mail = new Message;
		$mail->setFrom('Franta <franta@example.com>')
			->addTo('petr@example.com')
			->addTo('jirka@example.com')
			->setSubject('Potvrzení objednávky')
			->setBody("Dobrý den,\nvaše objednávka byla přijata.");

		$logger->log('test', $mail);

		$year = date('Y');
		$month = date('m');
		$day = date('d');

		$this->assertEquals(['.', '..', $year], scandir(vfsStream::url('home/sm')));
		$this->assertEquals(['.', '..', "$year-$month"], scandir(vfsStream::url("home/sm/$year")));
		$this->assertEquals(['.', '..', "$year-$month-$day"], scandir(vfsStream::url("home/sm/$year/$year-$month")));
		$file = scandir(vfsStream::url("home/sm/$year/$year-$month/$year-$month-$day"))[2];
		$file = vfsStream::url("home/sm/$year/$year-$month/$year-$month-$day/$file");

		$this->assertMailEqualsFile($file, $mail);
	}



	/**
	 * @param string $file
	 * @param Message $mail
	 */
	private function assertMailEqualsFile($file, $mail)
	{
		$this->assertEquals(Strings::replace(file_get_contents($file), '~Message\-ID\:[^\>]+\>~', '~skiped~'),
			Strings::replace($mail->generateMessage(), '~Message\-ID\:[^\>]+\>~', '~skiped~')
		);
	}

}
