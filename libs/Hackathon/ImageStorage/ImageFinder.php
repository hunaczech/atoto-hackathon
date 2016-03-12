<?php

namespace Hackathon\Image;

use Nette;
use Nette\Object;
use Hackathon\ImageStorage\DataManager;
use Hackathon\ImageStorage\Configuration;
use Nette\Utils\UnknownImageFileException;
use Hackathon\ImageStorage\ProfilesLoader;
use Hackathon\ImageStorage\Exceptions\UndefinedProfileException;
use Hackathon\ImageStorage\Exceptions\ImageIdNotProvidedException;



class Finder extends Object
{

	/**
	 * @var string
	 */
	protected $storagePath;

	/**
	 * @var array
	 */
	protected $profiles;

	/**
	 * @var Configuration
	 */
	private $imageStorageConfiguration;

	/**
	 * @var ProfilesLoader
	 */
	private $profilesLoader;

	/**
	 * @var DataManager
	 */
	private $dataManager;



	/**
	 * Finder constructor.
	 *
	 * @param Configuration $imageStorageConfiguration
	 * @param ProfilesLoader $profilesLoader
	 * @param DataManager $dataManager
	 */
	public function __construct(
		Configuration $imageStorageConfiguration,
		ProfilesLoader $profilesLoader,
		DataManager $dataManager
		)
	{
		$this->imageStorageConfiguration = $imageStorageConfiguration;
		$this->profilesLoader = $profilesLoader;

		$this->storagePath = $this->imageStorageConfiguration->get('storagePath');
		$this->profiles = $this->profilesLoader->get('profiles');
		$this->dataManager = $dataManager;
	}



	/**
	 * @param int $id
	 * @param string|NULL $profile
	 * @throws ImageIdNotProvidedException
	 * @throws Nette\Utils\UnknownImageFileException
	 * @throws UndefinedProfileException
	 */
	public function find($id, $profile=NULL)
	{
		if ($id === NULL) {
			throw new ImageIdNotProvidedException("You didn't provider image ID");
		}

		if($profile !== NULL && !array_key_exists($profile, $this->profiles)) {
			throw new UndefinedProfileException("This profile is not defined");
		}

		try {
			$this->dataManager->get($id,$profile);
		} catch(UnknownImageFileException $e)   {
			$this->dataManager->usePlaceholder();
		}

	}



	/**
	 * @param int $id
	 * @return array
	 */
	public function getInfo($id)
	{
		return $this->dataManager->getInfo($id);
	}
}
