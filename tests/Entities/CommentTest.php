<?php


namespace App\Tests\Features;


use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Tests\Entities\UnitTestSetUp;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentTest extends UnitTestSetUp
{
    /**
     * @group unitTest
     * @test
     */
    public function test_comment_is_added_and_flush_in_DB(){
        //Retrieve one Post

        $post = $this->postRepository->find(1);

        //Retrieve a User (Not necessarily the one who created the post)
        $user = $this->userRepository->find(1);

        //Create Comment
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
        $this->assertSame("COMMENTAIREES", $commentDB->getContent());
    }

}