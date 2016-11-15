<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Nette\Mailing;

use ArrayIterator;
use Nette;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit_Framework_TestCase;


/**
 * @author Martin Takáč <martin@takac.name>
 */
class FileBaseMessageTemplateProviderTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var vfsStreamDirectory
	 */
	private $root;


	function setUp()
	{
		$structure = [
			'foo' => [
				'empty' => '',
				'mistake-1' => "a\nb",
				'mistake-missing-subject' => "a\n\nLorem ipsum doler ist",
				'mistake-missing-content' => "a\n\n",
				'first' => "Subject: Cicero\n\nLorem ipsum doler ist",
				'second.txt' => "Subject: Cicero\n\nLorem ipsum doler ist",
			]
		];
		$this->root = vfsStream::setup('home', NULL, $structure);
	}



	function testEmptyPathIsFail()
	{
		$this->setExpectedException('Nette\Utils\AssertionException',
				'The variable expects to be string in range 1.., string \'\' given.');
		new FileBaseMessageTemplateProvider('');
	}



	function testTemplateIsNotFound()
	{
		$this->setExpectedException('RuntimeException',
				'Template `not-found\' is not found (vfs://foo/not-found).');
		$provider = new FileBaseMessageTemplateProvider(vfsStream::url('foo'));
		$provider->load('not-found');
	}



	function testEmptyTemplateIsMistake()
	{
		$this->setExpectedException('RuntimeException',
				'Template content is mistake. Must contain line with subject, two empty line, and next is content.');
		$provider = new FileBaseMessageTemplateProvider(vfsStream::url('home/foo'));
		$provider->load('empty');
	}



	function testIncorectFormatOfTemplate()
	{
		$this->setExpectedException('RuntimeException',
				'Template content is mistake. Must contain line with subject, two empty line, and next is content.');
		$provider = new FileBaseMessageTemplateProvider(vfsStream::url('home/foo'));
		$provider->load('mistake-1');
	}



	function testMissingSubject()
	{
		$this->setExpectedException('RuntimeException', 'Template subject is mistake. Subject must starts word: `Subject:\'.');
		$provider = new FileBaseMessageTemplateProvider(vfsStream::url('home/foo'));
		dump($provider->load('mistake-missing-subject'));
	}



	function testMissingContent()
	{
		$this->setExpectedException('RuntimeException', 'Template content is mistake. Content missing.');
		$provider = new FileBaseMessageTemplateProvider(vfsStream::url('home/foo'));
		$provider->load('mistake-missing-content');
	}



	function testCorrect()
	{
		$provider = new FileBaseMessageTemplateProvider(vfsStream::url('home/foo'));
		$res = $provider->load('first');
		$this->assertEquals("Cicero", $res->getSubject());
		$this->assertEquals("Lorem ipsum doler ist", $res->getBody());
	}



	function testCorrectWithExtension()
	{
		$provider = new FileBaseMessageTemplateProvider(vfsStream::url('home/foo'), 'txt');
		$res = $provider->load('second');
		$this->assertEquals("Cicero", $res->getSubject());
		$this->assertEquals("Lorem ipsum doler ist", $res->getBody());
	}

}
