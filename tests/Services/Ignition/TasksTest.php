<?php
namespace Rocketeer\Services\Ignition;

use Rocketeer\Tasks\Closure;
use Rocketeer\TestCases\RocketeerTestCase;

class TasksTest extends RocketeerTestCase
{
	public function testCustomTasksAreProperlyBoundToContainer()
	{
		$userTasks = (array) $this->app['config']->get('rocketeer::hooks.custom');
		$this->app['rocketeer.igniter.tasks']->registerTasksAndCommands($userTasks);

		$this->assertInstanceOf('Rocketeer\Dummies\MyCustomTask', $this->app['rocketeer.tasks.my-custom-task']);
	}

	public function testCanComputeSlugWithoutTask()
	{
		$slug = $this->app['rocketeer.igniter.tasks']->getTaskHandle('foobar');

		$this->assertEquals('foobar', $slug);
	}

	public function testCanComputeSlugWithClosureTask()
	{
		$task = new Closure($this->app);
		$slug = $this->app['rocketeer.igniter.tasks']->getTaskHandle('foobar', $task);

		$this->assertEquals('foobar', $slug);
	}
}
