<?php

namespace MFB\FeedbackBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use MFB\FeedbackBundle\Specification\SpecificationInterface;
use MFB\FeedbackBundle\Specification as Spec;

/**
 * FeedbackRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeedbackRepository extends EntityRepository implements FeedbackRepositoryInterface
{
    protected $qb;

    public function getLastEnabledFeedbacks(SpecificationInterface $specification, $num = 4)
    {
        return $this->match(
            new Spec\LimitLastN(
                $specification,
                $num
            )
        );
    }

    public function getFeedbackCount(SpecificationInterface $spec)
    {
        return $this->match(
            new Spec\AsSingleScalar($spec)
        );
    }

    /**
     * @todo this is not working correctly
     * @param SpecificationInterface $spec
     * @return array
     */
    public function getRatingsAverage(SpecificationInterface $spec)
    {

        return $this->match(
            new Spec\AsSingleScalar($spec, 'avg')
        );
    }

    /**
     * @todo this is not working correctly
     * @param SpecificationInterface $spec
     * @return array
     */
    public function getFeedbacksWithRatings(SpecificationInterface $spec)
    {
        return $this->match(
            new Spec\AsSingleScalar($spec)
        );
    }

    public function getRatingCount($accountChannel)
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT COUNT(fb.id) FROM MFBFeedbackBundle:Feedback fb WHERE fb.channelId = ?1 AND fb.isEnabled = 1 AND fb.rating  IS NOT NULL');
        $query->setParameter(1, $accountChannel->getId());
        return  $query->getSingleScalarResult();
    }

    public function getPlainRatingsAverage($accountChannel)
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT AVG(fb.rating) FROM MFBFeedbackBundle:Feedback fb WHERE fb.channelId = ?1 AND fb.isEnabled = 1');
        $query->setParameter(1, $accountChannel->getId());
        return round($query->getSingleScalarResult(), 1);
    }

    /**
     * Matcher by specified specification
     *
     * @param SpecificationInterface $specification
     * @return array
     */
    public function match(SpecificationInterface $specification)
    {
        $dqAlias  = 'fb';
        $qb = $this->createQueryBuilder($dqAlias);

        if ($specification instanceof Spec\AsInterface) {
            $specification->select($qb, $dqAlias);
        }

        if ($specification instanceof Spec\OrderInterface) {
            $specification->orderBy($qb, $dqAlias);
        }

        $expr = $specification->match($qb, $dqAlias);
        $query = $qb->where($expr)->getQuery();

        $specification->modifyQuery($query);

        return $query->getResult($query->getHydrationMode());
    }

}
