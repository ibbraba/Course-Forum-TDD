<?php


namespace App\Tests\Features;


use App\Entity\Post;
use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FindUserPostsTest extends WebTestCase
{
    protected $databaseTool;

    protected $doctrine;

    protected $userRepository;

    protected $postRepository;
    protected $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        self::bootKernel();



        $this->doctrine = static::getContainer()->get('doctrine')->getManager();

        /*        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();*/


        $user= $this->databaseTool->loadAliceFixture([__DIR__."\UserFixtures.yaml"]);

        $this->userRepository= $this->doctrine->getRepository(User::class);
        $this->postRepository= $this->doctrine->getRepository(Post::class);

    }



    /**
     * @test
     * @group integration
     */
    public function test_get_all_user_post(){
        $view = $this->postRepository->findBy([
            "auteur" => 1
        ]);

        $crawler = $this->client->request("GET", "/posts/user/1");


        // GET ALL Messages
       $posts = $crawler->filter("div.single-post");

        //TEST Number of messages in this page
        $this->assertCount(5, $posts);


    }

}