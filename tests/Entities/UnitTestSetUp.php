<?php


namespace App\Tests\Entities;


use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UnitTestSetUp extends WebTestCase
{
    protected $doctrine;
    protected $databaseTool;
    protected $postRepository;
    protected $userRepository;
    protected $likeRepository;
    protected $commentRepository;


    protected function setUp(): void
    {

        $this->doctrine = static::getContainer()->get('doctrine')->getManager();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        $this->databaseTool->loadAliceFixture([__DIR__."/UserFixtures.yaml"]);
        $this->postRepository = $this->doctrine->getRepository(Post::class);
        $this->userRepository = $this->doctrine->getRepository(User::class);
        $this->likeRepository = $this->doctrine->getRepository(Like::class);
        $this->commentRepository= $this->doctrine->getRepository(Comment::class);
        parent::setUp(); // TODO: Change the autogenerated stub

    }

    protected function tearDown(): void
    {
        $purger = new ORMPurger($this->doctrine);
        $purger->purge();
    }

}