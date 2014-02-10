<?php
namespace MFB\ChannelBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ChannelCriteriaRepository extends EntityRepository
{
    public function findAllUnusedRatingCriterias($channelId)
    {
        $unusedCriteriaRule = $this->unusedCriteriaRule($channelId);
        return $this->findAllRatingCriterias($unusedCriteriaRule);
    }

    public function findAllUnusedCriteriasForServices($channelId, $serviceIds)
    {
        $expr = $this->getExpression();
        $usedServiceIdsRule = $expr->in('stc.serviceType', $serviceIds);

        $rule = $expr->andX($this->unusedCriteriaRule($channelId), $usedServiceIdsRule);

        return $this->findAllRatingCriterias($rule);
    }


    public function findAllUsedRatingCriteriaIds($channelId)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT IDENTITY (e.ratingCriteria) FROM MFBChannelBundle:ChannelRatingCriteria e WHERE e.channel = {$channelId}"
        );
        $result = $query->getResult();
        return $this->mergeToSingleArray($result);
    }

    public function getUsedCriteriaCount($channelId)
    {
        $unusedCriteriaIds = $this->findAllUsedRatingCriteriaIds($channelId);
        return count($unusedCriteriaIds);
    }

    private function findAllRatingCriterias($rules)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $builder = $qb->select('e')->from('MFBRatingBundle:Rating', 'e');
        $builder->join('e.serviceTypeCriteria', 'stc');

        if (!empty($rules)) {
            $builder->where($rules);
        }

        return $builder->getQuery()->getResult();
    }

    private function mergeToSingleArray($result)
    {
        $criteriaIds = array();
        foreach ($result as $singleId) {
            $criteriaIds = array_merge($criteriaIds, $singleId);
        }
        return $criteriaIds;
    }

    private function unusedCriteriaRule($channelId)
    {
        $rule = null;
        $usedCriteriasIds = $this->findAllUsedRatingCriteriaIds($channelId);
        $expr = $this->getExpression();
        if (!empty($usedCriteriasIds)) {
            $rule = $expr->notIn('e.id', $usedCriteriasIds);
        }
        return $rule;
    }

    private function getExpression()
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->expr();
    }
}
