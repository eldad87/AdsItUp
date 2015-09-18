<?php
namespace AppBundle\Services\Platform;

use AppBundle\Entity\Brand;
use AppBundle\Services\Platform\Exception\InvalidSettingException;
use AppBundle\Services\Platform\Exception\MissingPlatformSupportException;
use Symfony\Component\DependencyInjection\ContainerAware;

class PlatformFactory extends ContainerAware {

	/**
	 * @param Brand $brand
	 * @return PlatformAbstract
	 * @throws InvalidSettingException
	 * @throws MissingPlatformSupportException
	 */
	public function create(Brand $brand)
	{
		/** @var PlatformAbstract $platform */
		$platform = $this->container->get($brand->getPlatform()->getName());
		if(!$platform) {
			throw new MissingPlatformSupportException(sprintf('Unknown platform [%s] is given for brand %d',
				$brand->getPlatform()->getName(), $brand->getId()));
		}
		$platform->setBrand($brand);

		$brandsSetting = $this->container->getParameter(sprintf('brand.%d', $brand->getId()));
		$settingClass = sprintf('AppBundle\Services\Platform\Setting\%sSetting', $brand->getPlatform()->getName()); //Spot -> SpotSetting
		$setting = new $settingClass($brandsSetting);
		$platform->setSetting($setting);

		return $platform;
	}
}