<?php

namespace App\Presenters;

use Hackathon\Image\Finder;
use Hackathon\ImageStorage\Exceptions\ImageIdNotProvidedException;



class InfoPresenter extends BasePresenter   {

	/**
	 * @var Finder
	 */
	private $imageFinder;



	/**
	 * InfoPresenter constructor.
	 *
	 * @param Finder $imageFinder
	 */
	public function __construct(Finder $imageFinder)
	{
		$this->imageFinder = $imageFinder;
	}



	/**
	 * @param $id
	 * @param string|NULL $profile
	 * @throws ImageIdNotProvidedException
	 */
	public function renderDefault($id,$profile=NULL)  {
		if($id === NULL)    {
			throw new ImageIdNotProvidedException("You didn't provide ID of image to us - what should we do :-(");
		}

		$this->setJsonResponse($this->imageFinder->getInfo($id,$profile));


	}
}
