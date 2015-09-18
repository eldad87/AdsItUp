<?php
namespace AppBundle\Services;

use AppBundle\Entity\Offer AS OfferEntity;
use AppBundle\Entity\OfferBanner;
use AppBundle\Entity\OfferClick;
use AppBundle\Entity\User;
use AppBundle\Services\Platform\PlatformAbstract;
use AppBundle\Services\Platform\PlatformFactory;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Util\StringUtils;
use UAParser\Parser;

class Offer extends ContainerAware
{
    /** @var TokenStorage  */
    protected $tokenStorage;
    /** @var Router */
    protected $router;
    /** @var AbstractManagerRegistry */
    protected $doctrine;
    /** @var  */
    protected $maxMindDB;

    public function setTokenStorage(TokenStorage $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }
    public function setDoctrine(AbstractManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }
    public function setRouter(Router $router) {
        $this->router = $router;
    }
    public function setMaxMindDB($maxMindDB) {
        $this->maxMindDB = $maxMindDB;
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

        /** @var \AppBundle\Entity\User $user */
        $user = $this->doctrine
            ->getRepository('AppBundle:User')
            ->find($parameters['userId']);
        if(!$user) {
            return false;
        }

        /** @var \AppBundle\Entity\Offer $offer */
        $offer = $this->doctrine
            ->getRepository('AppBundle:Offer')
            ->find($parameters['offerId']);
        if(!$offer) {
            return false;
        }
        if($offer->getBrand()->getId() != $user->getBrand()->getId()) {
            return false;
        }

        $offerClick = new OfferClick();
        $offerClick->setUser($user);
        $offerClick->setOffer($offer);

        $offerClick->setUaRaw($request->headers->get('User-Agent'));
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

        //User Agent
        $parser = Parser::create();
        $ua = $parser->parse($request->headers->get('User-Agent'));
        $offerClick->setUa($ua->ua->family); // Safari
        $offerClick->setUaVersion($ua->ua->toVersion()); //6.2.1
        $offerClick->setOs($ua->os->family); // Macx OS X
        $offerClick->setOsVersion($ua->os->toVersion()); //10
        $offerClick->setDevice($ua->device->family); //Other

        //IP Base location
        $offerClick->setIp($request->getClientIp());
        try {
            // Maxmind GeoIP2 Provider: e.g. the database reader
            $reader   = new \GeoIp2\Database\Reader($this->maxMindDB);
            $city = $reader->city($request->getClientIp());
            if($city->country->isoCode) {
                $offerClick->setCountryCode($city->country->isoCode);
            }
            if($city->mostSpecificSubdivision->isoCode) {
                $offerClick->setSubdivisionCode($city->mostSpecificSubdivision->isoCode);
            }
            if($city->city->name) {
                $offerClick->setCity($city->city->name);
            }
        } catch(\Exception $e) {
        }

        $this->doctrine->getManager()->persist($offerClick);
        $this->doctrine->getManager()->flush();

        /** @var PlatformFactory $platformFactory */
        $platformFactory = $this->container->get('PlatformFactory');
        /** @var PlatformAbstract $platform */
        $platform = $platformFactory->create($offer->getBrand());
        return $platform->handleClick($offerClick);
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
}