<?php


namespace App\Tests\Features;


use App\Entity\Post;
use App\Entity\User;
use App\Tests\DatabaseDependenciesTestCase;
use App\Tests\DatabasePrimer;
use Doctrine\ORM\EntityManager;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreatePostTest extends WebTestCase
{

    protected $databaseTool;

    protected $doctrine;

    protected $userRepository;


    protected function setUp(): void
    {



        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        self::bootKernel();

        $this->doctrine = static::getContainer()->get('doctrine')->getManager();

/*        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();*/


        $user= $this->databaseTool->loadAliceFixture([__DIR__."\UserFixtures.yaml"]);

        $this->userRepository= $this->doctrine->getRepository(User::class);




        parent::setUp(); // TODO: Change the autogenerated stub
    }

    /**
     * @test
     * @group integration
     */
    public function test_if_post_is_created_by_a_user(){
        $post = new Post();

        $user = $this->userRepository->find(1);




        $post->setAuteur($user)
            ->setContent("User 1 ")
            ->setTitle("Test");


        $this->assertInstanceOf(Post::class, $post);
    }

}