<?php

namespace App\Repository;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Like $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Like $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Returns true if the post is already liked, false if not
     */
    public function checkLike(Post $post, User $user) :bool{
        // Check if post is already liked by User
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT l
            FROM App\Entity\Like l
            WHERE l.post = :post
            AND l.user = :user        
        ")
            ->setParameter('post', $post)
            ->setParameter('user', $user);

        $result = $query->getResult();

        return $result ? true : false;
    }

    public function countLikesOnPost(int $id){

        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT p
            FROM App\Entity\Like p
            WHERE p.post = :id        
        ")
        ->setParameter('id', $id);

        return count($query->getResult());


    }
}
