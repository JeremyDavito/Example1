<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $admin = new User();
        $admin->setEmail('admin@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('$2y$13$6i5TZvMJBqr7S21K08QdQec672Gc04/lYQjK21wmPMHDfbiajwAL.'); // mdp = test
        $admin->setFirstname('admin');
        $admin->setLastname('oclock');
        $admin->setIsActivate(1);


        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@gmail.com');
        $user->setRoles([]);
        $user->setPassword('$2y$13$6i5TZvMJBqr7S21K08QdQec672Gc04/lYQjK21wmPMHDfbiajwAL.'); // mdp = test
        $user->setFirstname('user');
        $user->setLastname('oclock');
        $user->setIsActivate(1);


        $manager->persist($user);


        for ($index = 1; $index <= 10; $index++) {
            $category = new Category();
            $category->setName('catégorie ' . $index);
            $category->setPicture('category_placeholder.jpg');
            $manager->persist($category);
        }

        for ($index = 1; $index <= 10; $index++) {
            $event = new Event();
            $event->setTitle('Titre : ' . $index);
            $event->setDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam egestas bibendum ipsum, a blandit tellus aliquet eu. Sed ac odio non odio posuere auctor. Donec lobortis egestas aliquam. Duis venenatis.");
            $event->setPicture('event_placeholder.jpg');
            $event->setDate(new \DateTimeImmutable('+1 week'));
            $event->setAddress($index . ' rue des fixtures');
            $event->setZipcode($index < 10 ? 7500 . $index : 750 . $index);
            $event->setCity('Paris');
            $event->setCountry('France');
            $event->setMaxMembers(mt_rand(2, 50));
            $event->setIsArchived(false);
            $event->setIsOnline(false);
            $event->setCreatedAt(new \DateTimeImmutable());

            $event->setCategory($category);

            $event->setAuthor($user);
            $event->addMember($event->getAuthor());
            $manager->persist($event);
        }

        $manager->flush();
    }
}
