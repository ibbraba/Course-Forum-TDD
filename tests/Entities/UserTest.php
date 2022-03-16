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
        $user->setEmail("Username1@gmail.com");
        $user->setPassword("password");

        $userRepo = $this->entityManager->getRepository(User::class);



        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $users = $userRepo->findAll();

        $testUser = $userRepo->findOneBy([
            "email"=> "Username1@gmail.com"
        ]);


        // Check email is in DB
        $this->assertSame("Username1@gmail.com", $testUser->getEmail());


    }



}