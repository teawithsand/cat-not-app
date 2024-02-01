<?php

namespace App\DataFixtures;

use App\Entity\AppImage;
use App\Entity\AppUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new AppUser();
        $user->setPassword($this->hasher->hashPassword($user, "asdf"));
        $user->setUsername("asdf");
        $user->setRoles([]);
        $manager->persist($user);
        $manager->flush();

        $image = new AppImage;
        $image->setData("asdf");
        $image->setMime("image/png");
        $image->setOwner($user);
        $image->setRating(.75);
        $image->setCreatedAt(new \DateTimeImmutable);
        
        $manager->persist($image);
        $manager->flush();
    }
}
