<?php


namespace App\Tests\Entities;


use App\Tests\DatabaseDependenciesTestCase;
use App\Entity\User;

class UserTest extends UnitTestSetUp
{
    /**
     * @test
     * @group unitTest
     */
    public function test_new_user_is_created_in_db(){

        $user = new User();
        $user->setEmail("Username1@gmail.com");
        $user->setPassword("password");

        $this->doctrine->persist($user);
        $this->doctrine->flush();

        $testUser = $this->userRepository->findOneBy([
            "email"=> "Username1@gmail.com"
        ]);

        // Check User is in DB
        $this->assertSame("Username1@gmail.com", $testUser->getEmail());
    }

}