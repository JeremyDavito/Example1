<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use function get_class;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private $today;
    private $firstDayOfMonth;
    private $lastDayOfMonth;
    private $firstDayOfWeek;
    private $lastDayOfWeek;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);

        $this->today = date('Y-m-d', strtotime('today'));

        $this->firstDayOfMonth = date('Y-m-d', strtotime('first day of this month'));
        $this->lastDayOfMonth = date('Y-m-d', strtotime('last day of this month'));

        $this->firstDayOfWeek = date('Y-m-d', strtotime('monday this week'));
        $this->lastDayOfWeek = date('Y-m-d', strtotime('sunday this week'));
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Return users that have been created this month
     */
    public function findCreatedThisMonth()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.createdAt BETWEEN :firstDayOfMonth AND :lastDayOfMonth')
            ->setParameter(':firstDayOfMonth', $this->firstDayOfMonth)
            ->setParameter(':lastDayOfMonth', $this->lastDayOfMonth)
            ->getQuery()
            ->getResult();
    }

    /**
     * Return users that have been created the last 7 days
     */
    public function findCreatedThisWeek()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.createdAt BETWEEN :firstDayOfWeek AND :lastDayOfWeek')
            ->setParameter(':firstDayOfWeek', $this->firstDayOfWeek)
            ->setParameter(':lastDayOfWeek', $this->lastDayOfWeek)
            ->getQuery()
            ->getResult();
    }

    /**
     * Return users that have been created today
     */
    public function findCreatedToday()
    {
        $users = $this->findAll();

        $usersCreatedToday = [];

        foreach ($users as $user) {
            if ($user->getCreatedAt()->format('Y-m-d') === $this->today) {
                $usersCreatedToday[] = $user;
            }
        }

        return $usersCreatedToday;
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
