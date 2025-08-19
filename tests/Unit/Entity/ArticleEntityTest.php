<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class ArticleEntityTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private AbstractDatabaseTool $databaseTool;

    public function setUp(): void
    {
        // Init le kernel Symfony
        self::bootKernel();

        // On récupère l'EntityManager qu'on stock dans la propriété
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();

        $this->databaseTool->loadFixtures();
    }

    private function getUser(): User
    {
        return (new User)
            ->setUsername('Test')
            ->setPassword('password');
    }

    private function getArticle(): Article
    {
        return (new Article)
            ->setTitle('Titre de Test')
            ->setContent('Contenu de l\'article de test')
            ->setShortContent('Contenu court de l\'article de test')
            ->setUser($this->getUser())
            ->setEnabled(true);
    }

    private function persistData(Article $article, User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->persist($article);

        $this->entityManager->flush();
    }


    public function testGenerationSlugByTitle(): void
    {
        $article = $this->getArticle();

        $this->persistData($article, $article->getUser());

        $this->assertEquals('titre-de-test', $article->getSlug());
    }

    public function testGenerationCreatedAtOnPersist(): void
    {
        $article = $this->getArticle();

        $this->persistData($article, $article->getUser());

        $expected = (new \DateTimeImmutable())->format('Y-m-d H:i');

        $this->assertEquals($expected, $article->getCreatedAt()->format('Y-m-d H:i'));
    }

    public function testGenerationCreatedAtOnPersistWithExistingCreatedAt(): void // This test ensures that if createdAt is already set, it does not change
    {
        $createdAt = new \DateTimeImmutable('2023-01-01 12:00');

        $article = $this->getArticle()
            ->setCreatedAt($createdAt);

        $this->persistData($article, $article->getUser());

        $this->assertEquals($createdAt->format('Y-m-d H:i'), $article->getCreatedAt()->format('Y-m-d H:i'));
    }

    public function testGenerationUpdatedAtOnUpdate(): void // pour cela, on crée un article et on le met à jour pour vérifier que updatedAt est bien mis à jour
    {
        $article = $this->getArticle();

        $this->persistData($article, $article->getUser());

        $this->assertNull($article->getUpdatedAt()); // première validation intermédiaire

        $article
            ->setTitle('Nouveau titre');

        $this->entityManager->flush();

        $expected = (new \DateTimeImmutable())->format('Y-m-d H:i');

        $this->assertEquals($expected, $article->getUpdatedAt()->format('Y-m-d H:i'));
    }

    public function testGenerationUpdatedAtOnUpdateAndEnsureUpdatedIsChanged(): void
    {
        $article = $this->getArticle();

        $this->persistData($article, $article->getUser());

        $updatedAt = new \DateTimeImmutable('2025-01-01 12:00');

        $article
            ->setUpdatedAt($updatedAt);

        $this->entityManager->flush();

        $this->assertNotEquals($updatedAt, $article->getUpdatedAt());
    }

    public function testExceptionWhenNonUniqueTitle(): void
    {
        $this->databaseTool->loadAliceFixture(
            [
                \dirname(__DIR__) . '/Fixtures/ArticleFixtures.yaml'
            ]

        ); // charge des fixtures en yaml pour tester la contrainte unique

        $article = $this->getArticle()
            ->setTitle('titre de test');

        $this->expectException(UniqueConstraintViolationException::class);

        $this->persistData($article, $article->getUser());

    }
}
