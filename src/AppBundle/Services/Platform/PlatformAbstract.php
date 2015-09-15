<?php

namespace AppBundle\Services\Platform;

use AppBundle\Entity\OfferClick;
use Doctrine\Common\Persistence\AbstractManagerRegistry;

abstract class PlatformAbstract {
    /** @var AbstractManagerRegistry */
    protected $doctrine;

    public function setDoctrine(AbstractManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * Apply affiliate parameters
     *  to offer.destination and return it.
     *
     * @param OfferClick $offerClick
     * @return string URL
     */
    abstract public function handleClick(OfferClick $offerClick);
    //abstract public function handleLead();
    //abstract public function handleCustomer();
    //abstract public function handleDeposit();
    //abstract public function handleGame();

	/**
     * Append paremters to URL only if not exists!
	 * @param $url
	 * @param array $parameters
	 * @return string $url
	 */
    protected function appendParametersToURL($url, array $parameters=array())
    {
        $urlParts = parse_url($url);
        parse_str((isSet($urlParts['query']) ? $urlParts['query'] : ''), $params);
        $params = array_merge($parameters, $params);
        $urlParts['query'] = http_build_query($params);

        $url = $urlParts['scheme'] . '://' . $urlParts['host'];
        if(isSet($urlParts['path'])) {
            $url .= $urlParts['path'];
        }
        if(isSet($urlParts['query'])) {
            $url .= '?';
            $url .= $urlParts['query'];
        }

        return $url;
    }
}