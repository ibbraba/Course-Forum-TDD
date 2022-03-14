<?php


namespace App\Tests\Entities;


use App\Tests\DatabaseDependenciesTestCase;
use App\Entity\User;

class UserTest extends DatabaseDependenciesTestCase
{


    /**
     * @test
     * @group unitTest
     */
    public function test_new_user_is_created_in_db(){

        $user = new User();
        $user->setEmail("User1@gmail.com");
        $user->setPassword("password");

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userRepo = $this->entityManager->getRepository(User::class);
        $testUser = $userRepo->findOneBy([
            "id"=>2
        ]);

        $this->assertSame("User1@gmail.com", $testUser->getEmail());


    }



}