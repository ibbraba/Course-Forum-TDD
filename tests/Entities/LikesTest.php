<?php


namespace App\Tests\Entities;


use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LikesTest extends UnitTestSetUp
{
    /**
     * @test
     * @group unitTest
     */
    public function test_like_is_counted(){

        // Find a User and a  Post to create a like
        $post = $this->postRepository->find(1);
        $user = $this->userRepository->find(1);


        $like = new like();
        $like->setUser($user);
        $like->setPost($post);


        $this->doctrine->persist($like);
        $this->doctrine->flush();

        //Check if like is in DB
        $dbLike = $this->likeRepository->find(1);

        $this->assertEquals($user, $like->getUser());
        $this->assertEquals($post, $like->getPost());

    }

}