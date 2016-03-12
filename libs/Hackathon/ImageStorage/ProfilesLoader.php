<?php


namespace Hackathon\ImageStorage;


use Nette\Object;
use Nette\Utils\Json;



class ProfilesLoader extends Object
{

	/**
	 * @var array
	 */
	protected $configuration;

	public function __construct($configurationFilePath)
	{
		$this->configuration = file_get_contents($configurationFilePath);
	}



	/**
	 * @param bool $asArray
	 * @return mixed
	 * @throws \Nette\Utils\JsonException
	 */
	public function load($asArray=FALSE)  {
		return Json::decode($this->configuration,$asArray);
	}



	public function get($string)
	{
		$array = $this->load(TRUE);
		return $array[$string];
	}



	public function loadProfile($profile)
	{
		$array = $this->get('profiles');
		return $array[$profile];
	}
}
