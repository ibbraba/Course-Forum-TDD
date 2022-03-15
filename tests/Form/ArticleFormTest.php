<?php


namespace App\Tests\Form;


use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleFormTest extends WebTestCase
{

    protected $client;

    protected $databaseTool;

    protected $user;

    protected $userRepository;

    protected $postRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->databaseTool = $this->client->getContainer()
            ->get(DatabaseToolCollection::class)->get();

        $this->databaseTool->loadAliceFixture([__DIR__."\FormUserFixtures.yaml"]);

        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->postRepository = static::getContainer()->get(PostRepository::class);

        $this->user = $this->userRepository->findAll();;



/*        $this->client->loginUser($this->user);*/


    }

    /**
     * @test
     * @group Form
     */
    public function test_post_is_created_via_form(){

        // Request Page

        $crawler = $this->client->request("GET", "/app");

        $this->assertResponseStatusCodeSame(200);



        //Select and fill the form
        $form = $crawler->selectButton("Save")->form([
            "post[title]" => "OKTEST",
            "post[content]" => "Test Form Post",
        ]);

        //Tick the checkbox of the User
        $input= $form->get("post[auteur]")->select("1");


        $this->client->submit($form);



        // Find One By name
        $post = $this->postRepository->findOneBy([
            "title" => "OKTEST"
        ]);





        //Assert Post Exist
        $this->assertSame("OKTEST", $post->getTitle());


    }

}