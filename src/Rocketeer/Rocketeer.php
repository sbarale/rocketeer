<?php
/*
 * This file is part of Rocketeer
 *
 * (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Rocketeer;

use Rocketeer\Traits\HasLocator;

/**
 * Handles interaction between the User provided informations
 * and the various Rocketeer components
 *
 * @author Maxime Fabre <ehtnam6@gmail.com>
 */
class Rocketeer
{
	use HasLocator;

	/**
	 * The Rocketeer version
	 *
	 * @var string
	 */
	const VERSION = '2.0.1';

	/**
	 * Returns what stage Rocketeer thinks he's in
	 *
	 * @param string      $application
	 * @param string|null $path
	 *
	 * @return string|false
	 */
	public static function getDetectedStage($application = 'application', $path = null)
	{
		$current = $path ?: realpath(__DIR__);
		preg_match('/'.$application.'\/([a-zA-Z0-9_-]+)\/releases\/([0-9]{14})/', $current, $matches);

		return isset($matches[1]) ? $matches[1] : false;
	}

	//////////////////////////////////////////////////////////////////////
	//////////////////////////// CONFIGURATION ///////////////////////////
	//////////////////////////////////////////////////////////////////////

	/**
	 * Get the name of the application to deploy
	 *
	 * @return string
	 */
	public function getApplicationName()
	{
		return $this->config->get('rocketeer::application_name');
	}

	/**
	 * Get an option from Rocketeer's config file
	 *
	 * @param string $option
	 *
	 * @return string|array|Closure
	 */
	public function getOption($option)
	{
		$original = $this->config->get('rocketeer::'.$option);

		if ($contextual = $this->getContextualOption($option, 'stages', $original)) {
			return $contextual;
		}

		if ($contextual = $this->getContextualOption($option, 'connections', $original)) {
			return $contextual;
		}

		return $original;
	}

	/**
	 * Get a contextual option
	 *
	 * @param string            $option
	 * @param string            $type [stage,connection]
	 * @param string|array|null $original
	 *
	 * @return string|array|\Closure
	 */
	protected function getContextualOption($option, $type, $original = null)
	{
		// Switch context
		switch ($type) {
			case 'stages':
				$contextual = sprintf('rocketeer::on.stages.%s.%s', $this->connections->getStage(), $option);
				break;

			case 'connections':
				$contextual = sprintf('rocketeer::on.connections.%s.%s', $this->connections->getConnection(), $option);
				break;

			default:
				$contextual = sprintf('rocketeer::%s', $option);
				break;
		}

		// Merge with defaults
		$value = $this->config->get($contextual);
		if (is_array($value) && $original) {
			$value = array_replace($original, $value);
		}

		return $value;
	}
}
