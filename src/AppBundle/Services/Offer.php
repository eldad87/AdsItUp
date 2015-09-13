<?php
namespace AppBundle\Services;

use AppBundle\Entity\Offer AS OfferEntity;
use AppBundle\Entity\OfferBanner;
use AppBundle\Entity\User;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Util\StringUtils;


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

    public function getClickURL(OfferEntity $offer)
    {
        $data = array(
            'oid'   => $offer->getId(),
            'uid'    => $this->getUser()->getId(),
            't'      => time()
        );

        $transaction = $this->encodeTransaction($offer, $data);
        return $this->getTransactionURL($offer, $transaction);
    }

    public function getBannerClickURL(OfferBanner $banner)
    {
        $data = array(
            'oid'   => $banner->getOffer()->getId(),
            'bid'  => $banner->getId(),
            'uid'    => $this->getUser()->getId(),
            't'      => time()
        );

        $transaction = $this->encodeTransaction($banner->getOffer(), $data);

        return $this->getTransactionURL($banner->getOffer(), $transaction);
    }

    public function decodeTransaction($transaction)
    {
        $transaction = base64_decode($transaction);
        parse_str($transaction, $data);

        $offer = $this->doctrine
            ->getRepository('AppBundle:Offer')
            ->find($data['oid']);

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

    private function encodeTransaction(OfferEntity $order, array $data)
    {
        $data['sig'] = $this->generateSignature($order->getSalt(), $data);
        return base64_encode(http_build_query($data));
    }

    private function getTransactionURL(OfferEntity $offer, $transaction)
    {
        $url = $this->router->generate('offer.click',
            array(
                'transaction'   => $transaction
            )
        );

        return sprintf(
            '%s://%s%s',
            parse_url($offer->getDestination(), PHP_URL_SCHEME),
            $offer->getBrand()->getClickServerHost(),
            $url
        );
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