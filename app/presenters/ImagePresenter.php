<?php


namespace App\Presenters;


use Hackathon\Image\Finder;
use Hackathon\ImageStorage\ImageManager;
use Nette\Http\Request;
use Nette\Utils\Json;



class ImagePresenter extends BasePresenter
{

	/**
	 * @var Request
	 */
	private $httpRequest;

	/**
	 * @var Finder
	 */
	private $imageFinder;

	/**
	 * @var ImageManager
	 */
	private $imageManager;



	/**
	 * ImagePresenter constructor.
	 *
	 * @param Request $httpRequest
	 * @param Finder $imageFinder
	 * @param ImageManager $imageManager
	 */
	public function __construct(
		Request $httpRequest,
		Finder $imageFinder,
		ImageManager $imageManager
	) {
		$this->httpRequest = $httpRequest;
		$this->imageFinder = $imageFinder;
		$this->imageManager = $imageManager;
	}



	/**
	 * @param int $id
	 * @param string|NULL $profile
	 * @throws \Hackathon\ImageStorage\Exceptions\ImageIdNotProvidedException
	 * @throws \Nette\Utils\JsonException
	 */
	public function renderDefault($id, $profile = NULL)
	{
		$method = $this->httpRequest->getMethod();

		if ($method === 'GET') {
			$this->setJsonResponse($this->imageFinder->find($id, $profile));
		} elseif ($method === 'POST' && !empty($this->httpRequest->getRawBody())) {
			$this->setJsonResponse($this->imageManager->store(Json::decode($this->httpRequest->getRawBody(), TRUE)));
		} elseif ($method === 'DELETE') {
			$this->setJsonResponse($this->imageManager->flush(Json::decode($this->httpRequest->getRawBody(), TRUE)));
		} else {
			$this->setJsonResponse(['error' => 'Unsupported METHOD']);
		}
	}
}
