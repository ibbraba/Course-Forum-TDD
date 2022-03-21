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
        $this->userRepository = $this->doctrine->getRepository(User::class);

    }

    /**
     * @test
     * @group integration
     */
    public function test_non_logged_in_client_cannot_like_a_post(){
        //GET SINGLE PAGE
        $this->client->request("GET", "post/1");

        $this->assertResponseStatusCodeSame(200);


        //TEST Like Button not there
        $this->assertSelectorNotExists("button", "Like");
        $this->assertSelectorExists("p", "Connectez-vous et aimez ce post !");


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


    /**
     * @test
     * @group integration
     */
    public function test_user_can_leave_a_like_or_remove_it(){

        // User 3 does not likes the post
        $this->user = $this->userRepository->find(3);
        $this->client->loginUser($this->user);

        // Find a Post
        $id = 1;
        $post = $this->postRepository->find($id);


        $likes  = $this->likeRepository->countLikesOnPost($post->getId());

        // REQUEST SINGLE PAGE POST
        $crawler = $this->client->request("GET", "post/$id");



        //Check Likes count initially
        $this->assertSame(2, $likes);
        $this->assertSelectorTextContains("h5", "2 likes");


        // CLICK ON LIKE AND CHECK CHANGES
        $likeButton = $crawler->selectLink("Like")->link();
        $this->client->click($likeButton);
        $this->client->followRedirects();

        $newCount = $likes+1;
        $likesCount =  $this->likeRepository->countLikesOnPost(1);
        $this->assertSame($newCount, $likesCount);


        //CLICK AGAIN AND CHECK IF POST IS UNLIKED
        $newCount --;
        $this->client->click($likeButton);
        $this->client->followRedirects();
        $likesCount =  $this->likeRepository->countLikesOnPost(1);
        $this->assertSame($newCount, $likesCount);
    }




}