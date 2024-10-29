<?php
//
//namespace App\Repository;
//
//use App\Entity\Feedback;
//use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
//use Doctrine\Persistence\ManagerRegistry;
//
///**
// * @extends ServiceEntityRepository<Feedback>
// *
// * The ORM already provides generic find methods that can be used for querying the db :
// *
// * @method Feedback|null find($id, $lockMode = null, $lockVersion = null)
// * @method Feedback|null findOneBy(array $criteria, array $orderBy = null)
// * @method Feedback[]    findAll()
// * @method Feedback[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
// */
//class FeedbackRepository extends ServiceEntityRepository  {
//
//    public function __construct(ManagerRegistry $registry) {
//        parent::__construct($registry, Feedback::class);
//    }
//
//}

namespace App\Repository;

use App\Entity\Feedback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feedback>
 *
 * @method Feedback|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feedback|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feedback[]    findAll()
 * @method Feedback[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedbackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedback::class);
    }

    public function save(Feedback $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Feedback $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeAll(): void
    {
        $em = $this->getEntityManager();

        $entities = $this->findAll();
        foreach ($entities as $entity) {
            $em->remove($entity);
        }
        $em->flush();
    }


}

