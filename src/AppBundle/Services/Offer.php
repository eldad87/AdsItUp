<?php
namespace AppBundle\Services;

use AppBundle\Entity\Offer AS OfferEntity;
use AppBundle\Entity\OfferBanner;
use AppBundle\Entity\OfferClick;
use AppBundle\Entity\User;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Util\StringUtils;
use UAParser\Parser;

class Offer
{
    /** @var TokenStorage  */
    protected $tokenStorage;
    /** @var Router */
    protected $router;
    /** @var AbstractManagerRegistry */
    protected $doctrine;

    public function setTokenStorage(TokenStorage $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }
    public function setDoctrine(AbstractManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }
    public function setRouter(Router $router) {
        $this->router = $router;
    }

    public function getClickHost(OfferEntity $offer)
    {
        return $offer->getBrand()->getClickServerHost();
    }

    public function getClickParameters(OfferEntity $offer, $encoded=true)
    {
        $data = array(
            'offerId'   => $offer->getId(),
            'userId'    => $this->getUser()->getId(),
            'date'      => date('dmy'),
        );
        return $encoded ? array('transaction'=>$this->encode($offer, $data)) : $data;
    }

    public function getBannerClickParameters(OfferBanner $banner, $encoded=true)
    {
        $data = array(
            'offerId'   => $banner->getOffer()->getId(),
            'bannerId'  => $banner->getId(),
            'userId'    => $this->getUser()->getId(),
            'date'      => date('dmy')
        );

        return $encoded ? array('transaction'=>$this->encode($banner->getOffer(), $data)) : $data;
    }

    public function handleClick(Request $request, $transaction)
    {
        if(!is_null($transaction)) {
            $parameters = $this->decode($transaction);
        } else {
            $parameters = $request->query->all();
        }
        if(!$parameters) {
            return false;
        }

        /** @var \AppBundle\Entity\Offer $offer */
        $offer = $this->doctrine
            ->getRepository('AppBundle:Offer')
            ->find($parameters['offerId']);
        if(!$offer) {
            return false;
        }

        $offerClick = new OfferClick();
        $offerClick->setUaRaw($request->headers->get('User-Agent'));
        $offerClick->setOffer($offer);
        $offerClick->setBrand($offer->getBrand());
        if(isSet($parameters['bannerId'])) {
            /** @var OfferBanner $offerBanner */
            $offerBanner = $this->doctrine
                ->getRepository('AppBundle:OfferBanner')
                ->find($parameters['bannerId']);
            if(!$offerBanner || $offerBanner->getBrand()->getId() != $offer->getBrand()->getId()) {
                return false;
            }
            $offerClick->setOfferBanner($offerBanner);
        }

        $parser = Parser::create();
        $ua = $parser->parse($request->headers->get('User-Agent'));
        $offerClick->setIp($request->getClientIp());
        $offerClick->setUa($ua->ua->family); // Safari
        $offerClick->setUaVersion($ua->ua->toVersion()); //6.2.1
        $offerClick->setOs($ua->os->family); // Macx OS X
        $offerClick->setOsVersion($ua->os->toVersion()); //10
        $offerClick->setDevice($ua->device->family); //Other

        $this->doctrine->getManager()->persist($offerClick);
        $this->doctrine->getManager()->flush();
        return $offer->getDestination();
    }

    /**
     * @param OfferEntity $order
     * @param array $data
     * @return string
     */
    private function encode(OfferEntity $order, array $data)
    {
        $data['sig'] = $this->generateSignature($order->getSalt(), $data);
        return base64_encode((http_build_query($data)));
    }

    private function decode($transaction)
    {
        $transaction = base64_decode($transaction);
        parse_str($transaction, $data);

        $offer = $this->doctrine
            ->getRepository('AppBundle:Offer')
            ->find($data['offerId']);

        if(!$offer || !$this->validateSignature($offer->getSalt(), $data)) {
            return false;
        }

        return $data;
    }

    private function generateSignature($salt, array $data)
    {
        $data['salt'] = $salt;
        return md5(http_build_query($data));
    }

    private function validateSignature($salt, array $data)
    {
        $signature = $data['sig'];
        unset($data['sig']);

        $signature2 = $this->generateSignature($salt, $data);
        return StringUtils::equals($signature, $signature2);
    }

    /**
     * @return User
     */
    private function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    /**
     * @param $url
     * @param array $parameters
     * @return string $url
     */
    /*private function appendParametersToURL($url, array $parameters=array())
    {
        $urlParts = parse_url($url);
        parse_str((isSet($urlParts['query']) ? $urlParts['query'] : ''), $params);
        $params = array_merge($parameters, $params);
        $urlParts['query'] = http_build_query($params);
        //return http_build_url($urlParts);

        $url = $urlParts['scheme'] . '://' . $urlParts['host'];
        if(isSet($urlParts['path'])) {
            $url .= $urlParts['path'];
        }
        if(isSet($urlParts['query'])) {
            $url .= '?';
            $url .= $urlParts['query'];
        }

        return $url;
    }*/
}