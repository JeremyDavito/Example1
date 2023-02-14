<?php

namespace App\Repository;

use App\Entity\Event;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    private $today;
    private $firstDayOfMonth;
    private $lastDayOfMonth;
    private $firstDayOfWeek;
    private $lastDayOfWeek;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);

        $this->today = date('Y-m-d', strtotime('today'));

        $this->firstDayOfMonth = date('Y-m-d', strtotime('first day of this month'));
        $this->lastDayOfMonth = date('Y-m-d', strtotime('last day of this month'));

        $this->firstDayOfWeek = date('Y-m-d', strtotime('monday this week'));
        $this->lastDayOfWeek = date('Y-m-d', strtotime('sunday this week'));
    }

    /**
     * Return events that have been created this month
     */
    public function findCreatedThisMonth()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.createdAt BETWEEN :firstDayOfMonth AND :lastDayOfMonth')
            ->setParameter(':firstDayOfMonth', $this->firstDayOfMonth)
            ->setParameter(':lastDayOfMonth', $this->lastDayOfMonth)
            ->getQuery()
            ->getResult();
    }

    /**
     * Return events that have been created this week
     */
    public function findCreatedThisWeek()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.createdAt BETWEEN :firstDayOfWeek AND :lastDayOfWeek')
            ->setParameter(':firstDayOfWeek', $this->firstDayOfWeek)
            ->setParameter(':lastDayOfWeek', $this->lastDayOfWeek)
            ->getQuery()
            ->getResult();
    }

    /**
     * Return events that have been created today
     */
    public function findCreatedToday()
    {
        $events = $this->findAll();

        $createdTodayEvents = [];

        foreach ($events as $event) {
            if ($event->getCreatedAt()->format('Y-m-d') === $this->today) {
                $createdTodayEvents[] = $event;
            }
        }

        return $createdTodayEvents;
    }

    /**
     * Return events that happened or will happen this month
     */
    public function findHappensThisMonth()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.date BETWEEN :firstDayOfMonth AND :lastDayOfMonth')
            ->setParameter(':firstDayOfMonth', $this->firstDayOfMonth)
            ->setParameter(':lastDayOfMonth', $this->lastDayOfMonth)
            ->getQuery()
            ->getResult();
    }

    /**
     * Return events that happened or will happen this week
     */
    public function findHappensThisWeek()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.date BETWEEN :firstDayOfWeek AND :lastDayOfWeek')
            ->setParameter(':firstDayOfWeek', $this->firstDayOfWeek)
            ->setParameter(':lastDayOfWeek', $this->lastDayOfWeek)
            ->getQuery()
            ->getResult();
    }

    /**
     * Return events that happened or will happen today
     */
    public function findHappensToday()
    {
        $events = $this->findAll();

        $todayEvents = [];

        foreach ($events as $event) {
            if ($event->getDate()->format('Y-m-d') === $this->today) {
                $createdTodayEvents[] = $event;
            }
        }

        return $todayEvents;
    }

    /**
     * @return Event[] Returns an array of Event objects according to category
     */
    public function findByCategory(int $categoryId, ?int $limit)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.category = :categoryId')
            ->setParameter(':categoryId', $categoryId)
            ->andWhere('e.date > :today')
            ->setParameter(':today', new DateTimeImmutable())
            ->andWhere('e.isArchived = 0')
            ->orderBy('e.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[] Returns an array of Event objects according to a keyword
     */
    public function findByKeyword(string $keyword, ?int $limit)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.title LIKE :keyword')
            ->setParameter(':keyword', "%$keyword%")
            ->andWhere('e.date > :today')
            ->setParameter(':today', new DateTimeImmutable())
            ->andWhere('e.isArchived = 0')
            ->orderBy('e.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[] Returns an array of Event objects that are not archived
     *
     * findByActive(?int $limit) equals to findByActive(int $limit = null)
     */
    public function findByActive(int $limit = null)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isArchived = 0')
            ->andWhere('e.date > :today')
            ->setParameter(':today', new DateTimeImmutable())
            ->orderBy('e.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[] Returns an array of user past joined or created Event objects
     */
    public function findPastEvents(int $userId, int $limit = null)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.members', 'm')
            ->andWhere('m.id = :userId')
            ->setParameter(':userId', $userId)
            ->andWhere('e.date < :today')
            ->setParameter(':today', new DateTimeImmutable())
            ->orderBy('e.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[] Returns an array of user incoming joined or created Event objects
     */
    public function findIncomingEvents(int $userId, int $limit = null)
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.members', 'm')
            ->andWhere('m.id = :userId')
            ->setParameter(':userId', $userId)
            ->andWhere('e.date > :today')
            ->setParameter(':today', new DateTimeImmutable())
            ->orderBy('e.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[] Returns an array of similar Event objects
     */
    public function findRecommendedEvents(Event $event, int $limit = 3)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.category = :eventCategory')
            ->setParameter(':eventCategory', $event->getCategory())
            ->andWhere('e.date > :today')
            ->setParameter(':today', new DateTimeImmutable())
            ->andWhere('e.id != :eventId')
            ->setParameter(':eventId', $event->getId())
            ->orderBy('e.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }



    // /**
    //  * @return Event[] Returns an array of Event objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
