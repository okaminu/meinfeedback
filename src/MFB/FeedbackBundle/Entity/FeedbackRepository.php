<?php
namespace MFB\FeedbackBundle\Entity;

use Doctrine\ORM\EntityRepository;

class FeedbackRepository extends EntityRepository
{
    public function getChannelFeedbackCount($channelId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("COUNT(f.id)");
        $qb->from('MFBFeedbackBundle:Feedback', 'f');
        $qb->where($qb->expr()->eq('f.channelId', $channelId));
        $qb->andWhere($qb->expr()->eq('f.isEnabled', 1));
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getChannelRatingAverage($channelId, $minRating, $maxRating)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("AVG(fr.rating)");
        $qb->from('MFBFeedbackBundle:Feedback', 'f');
        $qb->join('f.feedbackRating', 'fr');
        $qb->where($qb->expr()->eq('f.channelId', $channelId));
        $qb->andWhere($qb->expr()->eq('f.isEnabled', 1));
        $qb->andWhere($qb->expr()->gte('fr.rating', $minRating));
        $qb->andWhere($qb->expr()->lte('fr.rating', $maxRating));
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getFeedbackRatingAverage($feedbackId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("AVG(r.rating)");
        $qb->from('MFBFeedbackBundle:Feedback', 'f');
        $qb->where($qb->expr()->eq('f.id', $feedbackId));
        $qb->leftJoin('f.feedbackRating', 'r');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getChannelCriteriaRatings($channelId, $minRating, $maxRating)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r.name AS name, AVG(fr.rating) AS rating');
        $qb->from('MFBFeedbackBundle:Feedback', 'f');
        $qb->join('f.feedbackRating', 'fr');
        $qb->join('fr.ratingCriteria', 'cr');
        $qb->join('cr.ratingCriteria', 'r');
        $qb->where($qb->expr()->eq('cr.channel', $channelId));
        $qb->andWhere($qb->expr()->eq('f.isEnabled', 1));
        $qb->andWhere($qb->expr()->gte('fr.rating', $minRating));
        $qb->andWhere($qb->expr()->lte('fr.rating', $maxRating));
        $qb->groupBy('r.name');
        return $qb->getQuery()->getArrayResult();
    }

}