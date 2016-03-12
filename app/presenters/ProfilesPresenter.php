<?php


namespace App\Presenters;


use Hackathon\ImageStorage\ProfilesLoader;



class ProfilesPresenter extends BasePresenter
{

	/**
	 * @var ProfilesLoader
	 */
	private $profilesLoader;



	/**
	 * ProfilesPresenter constructor.
	 *
	 * @param ProfilesLoader $profilesLoader
	 */
	public function __construct(ProfilesLoader $profilesLoader)
	{
		$this->profilesLoader = $profilesLoader;
	}



	public function renderDefault() {
		$this->setJsonResponse($this->profilesLoader->load());
	}
}
