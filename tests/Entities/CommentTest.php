<?php


namespace App\Tests\Features;


use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentTest extends WebTestCase
{
    protected $databaseTool;

    protected $doctrine;

    protected $userRepository;

    protected $postRepository;
    protected $client;
    private $commentRepository;

    protected function setUp(): void
    {

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        self::bootKernel();

        $this->doctrine = static::getContainer()->get('doctrine')->getManager();

        //LOAD USERS And POSTS

        $this->databaseTool->loadAliceFixture([__DIR__ . "\UserFixtures.yaml"]);

        $this->postRepository = $this->doctrine->getRepository(Post::class);
        $this->userRepository = $this->doctrine->getRepository(User::class);
        $this->commentRepository= $this->doctrine->getRepository(Comment::class);
    }


    /**
     * @group unitTest
     * @test
     */
    public function test_comment_is_added_and_flush_in_DB(){

        //Retrieve one Post

        $post = $this->postRepository->find(1);


        //Retrieve a User (Not necessarily the one who created the post)
        $user = $this->userRepository->find(1);



        //INPUT Text
        $comment = new Comment();
        $comment->setAuthor($user)
                ->setPost($post)
                ->setContent("COMMENTAIREES");

        $this->doctrine->persist($comment);
        $this->doctrine->flush($comment);




        //Check if comment is in DB
        $commentDB= $this->commentRepository->findOneBy([
            "author" => $user
        ]);

/*        dd($commentDB);*/
        $this->assertSame("COMMENTAIREES", $commentDB->getContent());
    }

}