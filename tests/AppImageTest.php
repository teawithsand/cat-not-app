<?php

namespace App\Tests;

use App\DataFixtures\AppFixtures;
use App\Entity\AppImage;
use App\Entity\AppUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class AppImageTest extends WebTestCase
{
    protected EntityManagerInterface $entityManager;
    public function setUp(): void
    {
        if(!self::$booted){
            self::createClient();
        }


        parent::setUp();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $this->entityManager->createQuery("DELETE FROM " . AppImage::class . "")->execute();
        $this->entityManager->createQuery("DELETE FROM " . AppUser::class . "")->execute();

        $fixture = new AppFixtures(self::getContainer()->get(UserPasswordHasherInterface::class));
        $fixture->load($this->entityManager);
    }

    public function tearDown(): void
    {
        $this->entityManager->createQuery("DELETE FROM " . AppImage::class . "")->execute();
        $this->entityManager->createQuery("DELETE FROM " . AppUser::class . "")->execute();
    }

    public function testHomePageLoad(): void
    {
        $client = static::getClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains("*", "or not");
    }
    public function testImageList(): void
    {
        $client = static::getClient();
        $user = $this->entityManager->getRepository(AppUser::class)->findOneBy(["username" => "asdf"]);
        $client->loginUser($user);

        $client->request("GET", "image/list");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains("*", "75%");
    }

    public function testImageShow(): void
    {
        $client = static::getClient();
        $image = $this->entityManager->getRepository(AppImage::class)->findOneBy([
            "rating" => .75
        ]);

        $client->request("GET", "/image/show/".$image->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame("Content-Type", $image->getMime());
    }

    public function testImageInfo(): void
    {
        $client = static::getClient();
        $image = $this->entityManager->getRepository(AppImage::class)->findOneBy([
            "rating" => .75
        ]);

        $client->request("GET", "/image/info/".$image->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains("*", "75%");
    }

    public function testUserLogout(): void
    {
        $client = static::getClient();
        $user = $this->entityManager->getRepository(AppUser::class)->findOneBy(["username" => "asdf"]);
        $client->request("GET", "/");
        $client->loginUser($user);

        $client->request("GET", "/logout");
        $client->request("GET", "/");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextNotContains("*", "logout");
    }

    public function testUserLogin(): void
    {
        $client = static::getClient();
        $client->request("GET", "/login");
        $this->assertResponseIsSuccessful();
    }

    public function testImageUpload(): void
    {
        $client = static::getClient();
        $user = $this->entityManager->getRepository(AppUser::class)->findOneBy(["username" => "asdf"]);
        $client->request("GET", "/");
        $client->loginUser($user);

        $client->request("GET", "/image/upload");
        $this->assertResponseIsSuccessful();

        $client->request("POST", "/image/upload", [], [
            "data" => new UploadedFile("/etc/passwd", "asdf.jpg", "image/jpeg", null, true)
        ], [
            "Content-Type" => "application/multipart+data"
        ], "");
        $this->assertResponseIsSuccessful();
    }
}
