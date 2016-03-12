<?php


namespace Hackathon\ImageStorage;


use Nette\Object;
use Nette\Utils\Image;
use Nette\Utils\DateTime;
use Nette\FileNotFoundException;



class DataManager extends Object
{

	const DIRECTORY_DEPTH = 2;
	const DIRECTORY_LENGTH = 2;

	/**
	 * @var string
	 */
	protected $filename;

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var ProfilesLoader
	 */
	private $profilesLoader;

	/**
	 * @var string
	 */
	protected $profileDirectory;



	/**
	 * DataManager constructor.
	 *
	 * @param Configuration $configuration
	 * @param ProfilesLoader $profilesLoader
	 */
	public function __construct(
		Configuration $configuration,
		ProfilesLoader $profilesLoader
	) {

		$this->configuration = $configuration;
		$this->profilesLoader = $profilesLoader;

		$this->profileDirectory = 'original/';
	}



	public function generateDirectory()
	{
		$uniqueStorageDirectory = $this->configuration->get('storagePath') . $this->profileDirectory;

		for ($i = 0; $i <= self::DIRECTORY_DEPTH; $i = $i + self::DIRECTORY_LENGTH) {
			$uniqueStorageDirectory = $uniqueStorageDirectory . substr($this->getFilename(), $i, 2) . '/';
			$this->processDirectory($uniqueStorageDirectory);
		}

		return $uniqueStorageDirectory;
	}



	public function getFilename()
	{
		return $this->filename;
	}



	public function generateFilename($id)
	{
		$this->setFilename($id . '.file');
		$storagePath = $this->generateDirectory();

		return $storagePath . $this->getFilename();
	}



	/**
	 * @param string $imageUrl
	 */
	public function saveOriginalImage($imageUrl)
	{
		$id = $this->generateIdFromUrl($imageUrl);
		$storagePath = $this->generateFilename($id);

		$originalImage = file_get_contents($imageUrl);
		file_put_contents($storagePath, $originalImage);
	}



	public function saveImageFromProfile($id, $profile)
	{
		try {
			$this->checkFile();
		} catch (FileNotFoundException $e) {
			$this->generateCachedImage($id, $profile);
		}

		$image = Image::fromFile($this->getFullPath());
		$image->send();
	}



	public function generateCachedImage($id, $profile)
	{
		$profileConfig = $this->profilesLoader->loadProfile($profile);
		$this->setProfileDirectory('original');
		$originalStoragePath = $this->generateDirectory();

		$image = Image::fromFile($originalStoragePath . $id . '.file');
		$image->resize($profileConfig['width'], $profileConfig['height'], Image::SHRINK_ONLY);

		$this->setProfileDirectory($profile);
		$storagePath = $this->generateFilename($id);
		file_put_contents($storagePath, (string) $image);
	}



	/**
	 * @param string $filename
	 */
	public function setFilename($filename)
	{
		$this->filename = $filename;
	}



	public function getInfo($id)
	{
		$this->generateFilename($id);
		$this->checkFile();

		return [
			'id'       => $id,
			'filesize' => filesize($this->getFullPath()),
			'updated'  => DateTime::from(filemtime($this->getFullPath())),
		];
	}



	private function checkFile()
	{
		if (!file_exists($this->getFullPath())) {
			throw new FileNotFoundException("Your file {$this->getFilename()} doesn't exist");
		}
	}



	public function deleteFile()
	{
		$this->checkFile();

		unlink($this->getFilename());

	}



	private function processDirectory($uniqueStorageDirectory)
	{
		if (!is_dir($uniqueStorageDirectory)) {
			mkdir($uniqueStorageDirectory);
		}
	}



	public function generateIdFromUrl($url)
	{
		return md5($url);
	}



	private function getFullPath()
	{
		return $this->generateDirectory() . $this->getFilename();
	}



	public function get($id, $profile = NULL)
	{
		$this->generateFilename($id);

		if ($profile !== NULL) {
			$this->getProfileForImage($id, $profile);
		}

		$this->loadImage();
	}



	private function getProfileForImage($id, $profile)
	{
		$this->setProfileDirectory($profile);
		$this->generateDirectory();
		$this->generateFilename($id);
		$this->saveImageFromProfile($id, $profile);
	}



	/**
	 * @return string
	 */
	public function getProfileDirectory()
	{
		return $this->profileDirectory;
	}



	/**
	 * @param string $profileDirectory
	 */
	public function setProfileDirectory($profileDirectory)
	{
		$this->profileDirectory = $profileDirectory . '/';
	}



	private function loadImage()
	{
		$image = Image::fromFile($this->getFullPath());
		$image->send();
	}



	public function usePlaceholder()
	{
		$placeholderUri = $this->profilesLoader->get('placeholderUri');
		if ($placeholderUri !== NULL) {
			$image = Image::fromFile($placeholderUri);
			$image->send();
		}
	}

}
