<?php


namespace App\Tests\Features;


use App\Entity\Post;
use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FindUserPostsTest extends FeaturesTestsSetup
{


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