<?php


namespace Hackathon\ImageStorage;


use Nette\Object;



class Configuration extends Object
{

	/**
	 * @var array
	 */
	protected $configuration;



	public function __construct($configuration)
	{
		$this->configuration = $configuration;
	}



	/**
	 * @param $configKey
	 * @return array
	 */
	public function get($configKey)
	{
		return $this->configuration[$configKey];
	}
}
