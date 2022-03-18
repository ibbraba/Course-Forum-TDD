<?php


namespace App\Tests\Entities;


use App\Entity\Post;
use App\Repository\PostRepository;
use App\Tests\DatabaseDependenciesTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostTest extends UnitTestSetUp
{
    // Test If Pst is created

    /**
     * @test
     * @group unitTest
     */
    public function test_new_post_is_created_and_flushed_in_DB(){

        //Create Post
        $post = new Post();

        $post->setTitle("Test")
            ->setContent("First Post");

        //Flush Post
        $this->doctrine->persist($post);
        $this->doctrine->flush($post);

        $postDB = $this->postRepository->findOneBy([
            "title" =>"Test"
        ]);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertSame($postDB->getContent(), "First Post");
    }

}