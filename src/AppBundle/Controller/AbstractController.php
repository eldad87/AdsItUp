<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Brand;
use AppBundle\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AbstractController extends Controller
{
    protected function checkAccess($entity=null)
    {
        $user = $this->getUser();
        /** @var Brand $brandByHost */
        $brandByHost = $this->get('Brand')->byHost();

        //Check user is in current brand
        if(!$brandByHost || $user->getBrand()->getId() != $brandByHost->getId()) {
            throw $this->createAccessDeniedException('Unable to access this page (1)!');
        }

        if(!$entity) {
            return true;
        }

        //Check entity - if in the same brand
        $brand = false;
        if($entity instanceof Brand) {
            $brand = $entity;
        } else if(method_exists($entity, 'getBrand')) {
            $brand = $entity->getBrand();
        }
        if(!$brand || $brand->getId() != $brandByHost->getId()) {
            throw $this->createAccessDeniedException('Unable to access this page (2)!');
        }

        //Check user level
        if(!$this->isGranted('ROLE_BRAND')) {
            $owner = false;
            if($entity instanceof User) {
                $owner = $entity;
            } else if(method_exists($entity, 'getUser')) {
                $owner = $entity->getUser();
            }

            if(!$owner) {
                true;
            }
            if ($this->isGranted('ROLE_AFFILIATE_MANAGER')) {
                if($owner->getManager()->getId() != $user->getId() && $owner->getId() != $user->getId()) {
                    throw $this->createAccessDeniedException('Unable to access this page (4)!');
                }
            } else {
                if($owner->getId() != $user->getId()) {
                    throw $this->createAccessDeniedException('Unable to access this page (5)!');
                }
            }

            return true;
        }
    }

    protected function applyPermission(QueryBuilder $qb)
    {
        $aliases = $qb->getRootAliases();
        $entities = $qb->getRootEntities();
        $entity = new $entities[0];

        if(method_exists($entity, 'getBrand')) {
            $qb->andWhere(sprintf('%s.brand = :brand', $aliases[0]));
            $qb->setParameter('brand', $this->get('Brand')->byHost());
        }

        if(!$this->isGranted('ROLE_BRAND')) {
            if('AppBundle\Entity\User' == $entities[0]) {
                if($this->isGranted('ROLE_AFFILIATE_MANAGER')) {
                    $qb->andWhere(sprintf('(%s.manager = :manager OR %s.manager IS NULL OR %s.id = :manager)', $aliases[0], $aliases[0], $aliases[0]));
                } else {
                    $qb->andWhere(sprintf('%s.id = :manager', $aliases[0]));
                }
                $qb->setParameter('manager', $this->getUser());
            } else if(method_exists($entity, 'getUser')) {
                if($this->isGranted('ROLE_AFFILIATE_MANAGER')) {
                    $qb->innerJoin(sprintf('%s.user', $aliases[0]), 'user');
                    $qb->andWhere(sprintf('(user.manager = :manager OR user.manager IS NULL OR %s.user = :manager)', $aliases[0]));
                } else {
                    $qb->andWhere(sprintf('%s.user = :manager', $aliases[0]));
                }
                $qb->setParameter('manager', $this->getUser());
            }
        }
    }
}