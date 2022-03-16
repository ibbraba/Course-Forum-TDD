<?php


namespace App\Tests\Features;


use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LeaveCommentTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private $client;
    private $commentRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->doctrine = static::getContainer()->get('doctrine')->getManager();

        $this->commentRepository = $this->doctrine->getRepository(Comment::class);


    }


    /**
     * @test
     * @group integration
     */
    public function test_comment_is_left_on_post(){
        $crawler = $this->client->request("GET", "post/1");

        $form = $crawler->selectButton("Send")->form([
            "comment_form[content]" => "Test Submit"
        ]);

        $this->client->submit($form);

        $this->client->followRedirect();

        //Check if reply appear in Post page
        $comments =  $crawler->filter("#comment");


        $nbComments = $this->count($comments);

        //SetAuthor and Post in the Controller
        $this->assertEquals(1, $nbComments);
    }

}