<?php


namespace App\Tests\Features;


use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LeaveCommentTest extends FeaturesTestsSetup
{


    /**
     * @test
     * @group integration
     */
    public function test_a_user_logged_in_can_reply(){

        //LOGIN USER
        $user = $this->userRepository->find(1);
        $this->client->loginUser($user);

        //FIND A POST PAGE
        $post = $this->postRepository->find(1);
        $postId = $post->getId();
        $url = "post/$postId";


        $this->client->request("GET", "post/$postId");


        //Check if reply form
        $this->assertSelectorExists("h2", "Repondre au post");
    }


    /**
     * @test
     * @group integration
     */
    public function test_anonymous_user_can_not_reply(){

/*        //LOGIN USER
        $user = $this->userRepository->find(1);
        $this->client->loginUser($user);*/



        //FIND A POST PAGE
        $post = $this->postRepository->find(1);
        $postId = $post->getId();



        $this->client->request("GET", "post/$postId");
        $this->assertResponseStatusCodeSame(200);

        //Check if reply form
//        $this->assertSelectorNotExists("h2", "Repondre au post");
        $this->assertSelectorExists("p", "Vous devez vous connecter pour répondre");
    }


    /**
     * @test
     * @group integration
     */
    public function test_comment_is_left_on_post(){
        //LOGIN USER
        $user = $this->userRepository->find(1);
        $this->client->loginUser($user);

        //GET PAGE
        $crawler = $this->client->request("GET", "post/1");
        $form = $crawler->selectButton("Send")->form([
            "comment_form[content]" => "Test Submit"
        ]);

        //SEND Comment
        $this->client->submit($form);
        $this->client->followRedirect();
        //Check if reply appear in Post page
        $comments =  $crawler->filter("#comment");


        $nbComments = $this->count($comments);


        $this->assertEquals(1, $nbComments);
    }

}