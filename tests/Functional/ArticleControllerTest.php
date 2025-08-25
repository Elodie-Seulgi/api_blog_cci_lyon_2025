<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;


class ArticleControllerTest extends WebTestCase
{
    // Propriété qui va stocker notre client léger (pour envoyer des requêtes)
    private KernelBrowser $client;

    private AbstractDatabaseTool $databaseTool;

    public function setUp(): void // le setup est executé avant chaque exécution de test, ça fonctionne un peu comme un construct
    {
        //TODO Création d'un client léger pour les tests
        $this->client = self::createClient();
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();

    }

    private function getUser(string $username = 'admin'): ?User
    {
        // On load les fixtures
        $this->databaseTool->loadAliceFixture([
            __DIR__ . '/UserFixtures.yaml'
        ]);

        // On récupère l'utilisateur par son nom d'utilisateur
        $user = self::getContainer()->get(UserRepository::class)
            ->findOneBy(['username' => $username]);

        // On le renvois
        return $user;
    }

    public function testIndexEndpointWithNoConnectedUser(): void // test fonctionnel 
    {
        $this->client->request('GET', '/api/admin/articles');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

    }

    /**
     * Cette méthode teste l'endpoint /api/admin/articles
     * avec un utilisateur connecté qui n'est pas un administrateur.
     * @return void
     */
    public function testIndexEndPointWithConnectedUser(): void
    {

        //On connecte l'utilisateur
        $this->client->loginUser($this->getUser('user'), 'login');

        // On envoie une requête GET à l'endpoint /api/admin/articles
        $this->client->request('GET', '/api/admin/articles');

        // On vérifie que la réponse a le code HTTP 403 (Forbidden)
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }


    /**
     * Cette méthode teste l'endpoint /api/admin/articles
     * avec un utilisateur connecté qui n'est pas un administrateur.
     * @return void
     */
    public function testIndexEndPointWithConnectedAdmin(): void
    {

        //On connecte l'utilisateur
        $this->client->loginUser($this->getUser('admin'), 'login');

        // On envoie une requête GET à l'endpoint /api/admin/articles
        $this->client->request('GET', '/api/admin/articles');

        // On vérifie que la réponse a le code HTTP 403 (Forbidden)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testIndexEndpointValidateStructureJsonResponse(): void
    {
        $this->client->loginUser(
            $this->getUser(),
            'login'
        );

        $this->client->request('GET', '/api/admin/articles');

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertisArray($response);
        $this->assertArrayHasKey('items', $response);
        $this->assertArrayHasKey('meta', $response);
        $this->assertArrayHasKey('pages', $response['meta']);
        $this->assertArrayHasKey('total', $response['meta']);

    }

    public function testIndexEndpointValidateNumberIfItemsDefault(): void
    {
        $this->client->loginUser(
            $this->getUser(),
            'login'
        );

        // On charge les fixtures
        $this->databaseTool->loadAliceFixture([
            __DIR__ . '/ArticleFixtures.yaml'
        ]);

        $this->client->request('GET', '/api/admin/articles');

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(6, $response['items']);



    }


}