<?php

namespace App\Presenters;

use Nette;
use Nette\Application\Responses\JsonResponse;



abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	/**
	 * @var array
	 */
	protected $jsonResponse;



	public function afterRender()
	{
		parent::afterRender();
		$this->sendResponse($this->getJsonResponse());
	}



	/**
	 * @return mixed
	 */
	public function getJsonResponse()
	{
		return new JsonResponse($this->jsonResponse);
	}



	/**
	 * @param mixed $jsonResponse
	 */
	public function setJsonResponse($jsonResponse)
	{
		$this->jsonResponse = $jsonResponse;
	}
}
