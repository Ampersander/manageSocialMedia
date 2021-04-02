<?php

namespace App\DataFixtures;
use App\Entity\Test;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TestFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 10; $i++){
            $post = new Test();
            $post->setReseau("Facebook n°$i")
                ->setContent("Ceci est un description n°$i")
                ->setImage("http://placehold.it/350x150")
                ->setDate("17/03/2021");
            
            $manager->persist($post);
        }

        $manager->flush();
    }
}
