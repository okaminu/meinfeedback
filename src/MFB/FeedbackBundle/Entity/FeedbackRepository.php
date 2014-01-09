<?php
namespace MFB\FeedbackBundle\Entity;

use Doctrine\ORM\EntityRepository;

class FeedbackRepository extends EntityRepository
{
    public function getChannelFeedbackCount($channelId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("COUNT(feedback.id)");
        $qb->from('MFBFeedbackBundle:Feedback', 'feedback');
        $qb->where($qb->expr()->eq('feedback.channelId', $channelId));
        $qb->andWhere($qb->expr()->eq('feedback.isEnabled', 1));
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getChannelRatingAverage($channelId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("AVG(rating.rating)");
        $qb->from('MFBFeedbackBundle:Feedback', 'feedback');
        $qb->where($qb->expr()->eq('feedback.channelId', $channelId));
        $qb->andWhere($qb->expr()->eq('feedback.isEnabled', 1));
        $qb->leftJoin('feedback.feedbackRating', 'rating');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getFeedbackRatingAverage($feedbackId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("AVG(rating.rating)");
        $qb->from('MFBFeedbackBundle:Feedback', 'feedback');
        $qb->where($qb->expr()->eq('feedback.id', $feedbackId));
        $qb->leftJoin('feedback.feedbackRating', 'rating');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getChannelRatings($channelId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("AVG(rating.rating)");
        $qb->from('MFBFeedbackBundle:Feedback', 'feedback');
        $qb->where($qb->expr()->eq('feedback.channelId', $channelId));
        $qb->andWhere($qb->expr()->eq('feedback.isEnabled', 1));
        $qb->leftJoin('feedback.feedbackRating', 'rating');
        return $qb->getQuery()->getSingleScalarResult();
    }

}