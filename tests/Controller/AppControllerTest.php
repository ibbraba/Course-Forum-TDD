<?php


namespace App\Tests\Controller;


use App\Entity\Post;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppControllerTest extends WebTestCase
{
    protected $client;

    protected $databaseTools;

    protected $doctrine;


    protected function setUp(): void
    {
        $this->client = static::createClient();
        self::bootKernel();

        $this->databaseTools = static::getContainer()->get(DatabaseToolCollection::class)->get();


        $this->doctrine = static::getContainer()->get('doctrine')->getManager();

        /*        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();*/


        $users= $this->databaseTools->loadAliceFixture([__DIR__."\UserFixtures.yaml"]);
        //Load User and Posts


        $posts = $this->databaseTools->loadAliceFixture([__DIR__."\PostFixtures.yaml"]);



    }

    /**
     * @test
     * @group Controller
     */
    public function test_get_posts_from_db(){

        // Load User

        //Create post with this User
        $posts = $this->doctrine->getRepository(Post::class);

        $allPosts = $posts->findAll();

        $this->assertCount(10, $allPosts);

    }



    /**
     * @test
     * @group Controller
     */
    public function test_access_to_page_with_posts(){


        //Handle 404

        $this->client->request("GET", "/app");

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains("span", "Hello World");
/*        $this->assertCount(10, $countPost);*/

    }







}