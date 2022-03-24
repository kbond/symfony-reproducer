<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\PropertyRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(PropertyRepository $repo): Response
    {
        $subQuery = \sprintf('SELECT i FROM %s i WHERE i.property = property AND i.type = :type ORDER BY i.sort', Image::class);

        $props = $repo->createQueryBuilder('property')
            ->addSelect('image')
            ->leftJoin('property.images', 'image', Join::WITH, \sprintf('image = FIRST(%s)', $subQuery))
            ->setParameter('type', 'bar')
            ->getQuery()
            ->execute()
        ;

        foreach ($props as $property) {
            dump($property->getImages()->count(), $property->profileImage());
        }

        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }
}
