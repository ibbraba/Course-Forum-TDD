<?php


namespace App\Tests\Features;


use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LeaveALikeTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private $client;
    private $doctrine;
    private $databaseTools;
    protected $commentRepository;
    private $postRepository;
    protected $userRepository;
    private $user;
    private $likeRepository;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->doctrine = static::getContainer()->get('doctrine')->getManager();

        $this->commentRepository = $this->doctrine->getRepository(Comment::class);


        $this->databaseTools = static::getContainer()->get(DatabaseToolCollection::class)->get();


        $this->databaseTools->loadAliceFixture([__DIR__."\UserFixtures.yaml"]);

        $this->postRepository = $this->doctrine->getRepository(Post::class);
        $this->likeRepository = $this->doctrine->getRepository(Like::class);
        $this->userRepository= $this->doctrine->getRepository(User::class);

        $this->user = $this->userRepository->find(1);
        $this->client->loginUser($this->user);

    }



    /**
     * @test
     * @group integration
     */
    public function test_user_like_post_or_not(){
        $user2 = $this->userRepository->find(1);
        $user3 = $this->userRepository->find(3);

        $post = $this->postRepository->find(1);


        //Check user 2 likes post => Return True
        $this->assertEquals(true, $this->likeRepository->checkLike($post, $user2));

        //Check user 3 does not likes post => Return false
        $this->assertEquals(false, $this->likeRepository->checkLike($post, $user3));

    }


    public function test_user_can_remove_a_like(){
            // REQUEST SINGLE PAGE POST

        // CLICKER SUR LIKE

        // METTRE A JOUR LA VUE

        //CHECK LIKE COUNT IN DB
    }


    /**
     * @test
     * @group integration
     */
    public function test_user_can_leave_a_like(){

        // Find a Post
        $id = 1;
        $post = $this->postRepository->find($id);

        $likes  = $this->likeRepository->countLikesOnPost($post->getId());

        // REQUEST SINGLE PAGE POST
        $crawler = $this->client->request("GET", "post/$id");



        //Check Likes count
        $this->assertSame(2, $likes);



        $this->assertSelectorTextContains("h5", "2 likes");
        // CLICKER SUR LIKE
        $likeButton = $crawler->selectLink("Like")->link();
        /*dd($likeButton);*/


        $this->client->click($likeButton);
        $this->client->followRedirect();

        // METTRE A JOUR LA VUE
        $newCount = $likes+1;

        //Check Ajax Update

        $this->assertSelectorTextContains("h5", "$newCount likes");
        //CHECK LIKE COUNT IN DB

        $likesCount =  $post->getLikes();

        $this->assertEquals($initLikesCount+1, $likesCount);

    }



}