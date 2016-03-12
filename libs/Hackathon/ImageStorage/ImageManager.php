<?php


namespace Hackathon\ImageStorage;


use Nette\Object;



class ImageManager extends Object
{

	/**
	 * @var array
	 */
	protected $inputProperties;

	/**
	 * @var array
	 */
	protected $requestData;

	/**
	 * @var array
	 */
	protected $requestStatus;

	/**
	 * @var DataManager
	 */
	private $dataManager;



	/**
	 * ImageManager constructor.
	 *
	 * @param DataManager $dataManager
	 */
	public function __construct(DataManager $dataManager)
	{
		$this->inputProperties['valid'] = TRUE;
		$this->dataManager = $dataManager;
	}



	/**
	 * @param array $dataArray
	 * @return array
	 */
	public function store(array $dataArray)
	{
		$this->setRequestData($dataArray);

		$this->validateInput();

		if ($this->inputProperties['valid'] === TRUE) {
			$this->downloadImage();
			$this->generateStorageInfo();
		}

		return $this->generateResponse();
	}



	private function validateInput()
	{
		// Validate that key URL is present
		if (!isset($this->requestData['url'])) {
			$this->inputProperties['validatorMessage'] = "Property URL is not provided - what am I supposed to do ???";
			$this->inputProperties['valid'] = FALSE;
		}
		// Validate that key URL really holds URL address of some image
		$regex = '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,}))\.?)(?::\d{2,5})?(?:[/?#]\S*)?$_iuS';
		preg_match($regex, $this->requestData['url'], $matches);
		if (count($matches) == 0) {
			throw new \Exception("Provided URL didn't pass the validation");
		}
		// check if host really exists
		if (!$this->checkUptime($this->requestData['url'])) {
			throw new \Exception("Provided URL sent error code in response - ignoring");
		}
		// check the file that it really is image
		if (count(getimagesize($this->requestData['url'])) < 7) {
			// display image
			throw new \Exception("Provided URL doesn't contain image - ignoring");
		}
	}



	private function generateStorageInfo()
	{
		$id = $this->dataManager->generateIdFromUrl($this->requestData['url']);
		$this->requestStatus = $this->dataManager->getInfo($id);
	}



	private function generateResponse()
	{

		return [
			'status'          => $this->requestStatus,
			'inputValidation' => $this->inputProperties,
		];
	}



	/**
	 * @return array
	 */
	public function getInputProperties()
	{
		return $this->inputProperties;
	}



	/**
	 * @return array
	 */
	public function getRequestData()
	{
		return $this->requestData;
	}



	/**
	 * @param array $requestData
	 */
	public function setRequestData($requestData)
	{
		$this->requestData = $requestData;
	}



	private function downloadImage()
	{
		$this->dataManager->saveOriginalImage($this->requestData['url']);
	}



	public function flush($dataArray)
	{
		$this->setRequestData($dataArray);

		$this->dataManager->generateFilename($this->requestData['url']);
		$this->dataManager->deleteFile();

		return [
			'filename' => $this->dataManager->getFilename(),
		];
	}



	private function checkUptime($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_exec($ch);
		$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if (200 == $retcode) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}
