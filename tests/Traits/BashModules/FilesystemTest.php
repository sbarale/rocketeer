<?php
namespace Rocketeer\Traits\BashModules;

use Rocketeer\TestCases\RocketeerTestCase;

class FilesystemTest extends RocketeerTestCase
{
	public function testCancelsSymlinkForUnexistingFolders()
	{
		$task   = $this->pretendTask();
		$folder = '{path.storage}/logs';
		$share  = $task->share($folder);

		$this->assertFalse($share);
	}

	public function testCanSymlinkFolders()
	{
		// Create dummy file
		$folder = $this->server.'/releases/20000000000000/src';
		mkdir($folder);
		file_put_contents($folder.'/source.txt', 'test');

		$task    = $this->pretendTask();
		$folder  = '{path.base}/source.txt';
		$share   = $task->share($folder);
		$matcher = array(
			sprintf('rm -rf %s', $this->server.'/shared//src/source.txt'),
			sprintf('ln -s %s %s', $this->server.'/releases/20000000000000//src/source.txt',$this->server.'/shared//src/source.txt'),
		);

		$this->assertEquals($matcher, $share);
	}

	public function testCanListContentsOfAFolder()
	{
		$contents = $this->task->listContents($this->server);

		$this->assertContains('current', $contents);
		$this->assertContains('releases', $contents);
		$this->assertContains('shared', $contents);
		$this->assertContains('state.json', $contents);
	}

	public function testCanCheckIfFileExists()
	{
		$this->assertTrue($this->task->fileExists($this->server));
		$this->assertFalse($this->task->fileExists($this->server.'/nope'));
	}

	public function testDoesntTryToMoveUnexistingFolders()
	{
		$this->pretendTask()->move('foobar', 'bazqux');

		$this->assertEmpty($this->history->getFlattenedOutput());
	}
}
