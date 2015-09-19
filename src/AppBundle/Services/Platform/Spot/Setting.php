<?php
namespace AppBundle\Services\Platform\Spot;

use AppBundle\Services\Platform\SettingAbstract;
use Symfony\Component\Validator\Constraints as Assert;

class Setting extends SettingAbstract {
	/**
	 * @Assert\NotBlank()
	 * @Assert\Url()
	 * @var string
	 */
	protected $url;
	/**
	 * @Assert\NotBlank()
	 * @var string
	 */
	protected $user;
	/**
	 * @Assert\NotBlank()
	 * @var string
	 */
	protected $password;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Type(
	 *     type="integer",
	 *     message="The value {{ value }} is not a valid {{ type }}."
	 * )
	 * @var int
	 */
	protected $campaignId;

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @return int
	 */
	public function getCampaignId()
	{
		return $this->campaignId;
	}
}