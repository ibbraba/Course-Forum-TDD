<?php


namespace App\Tests\Features;


use App\Entity\Post;
use App\Entity\User;
use App\Tests\DatabaseDependenciesTestCase;
use App\Tests\DatabasePrimer;
use Doctrine\ORM\EntityManager;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreatePostTest extends FeaturesTestsSetup
{


    /**
     * @test
     * @group integration
     */
    public function test_if_post_is_created_by_a_user_and_display(){

        //GET USER
        $user = $this->userRepository->find(1);
        $this->client->loginUser($user);
        //Get New post Page
        $crawler = $this->client->request("GET", "create-post");

        $this->assertResponseStatusCodeSame(200);


        $form = $crawler->selectButton("Save")->form([
           "post[title]" => "Test post",
           "post[content]" => "Very happy",
        ]);
        $this->assertNotNull($form);
        //Send the Post
        $this->client->submit($form);


        //Redirect and check if post is there
        $this->client->followRedirect();
/*        $newPost=  $crawler->filter(".post");*/

        $this->assertSelectorExists("h3", "Test post");


    }






}