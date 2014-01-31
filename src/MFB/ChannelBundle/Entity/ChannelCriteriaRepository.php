<?php
namespace MFB\ChannelBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ChannelCriteriaRepository extends EntityRepository
{
    public function findAllUnusedRatingCriterias($channelId)
    {
        $rule = null;
        $usedCriteriasIds = $this->findAllUsedRatingCriteriaIds($channelId);
        if (!empty($usedCriteriasIds)) {
            $rule = $this->getEntityManager()->createQueryBuilder()->expr()->notIn('e.id', $usedCriteriasIds);
        }
        return $this->findAllRatingCriterias($rule);
    }

    public function findAllUnusedCriteriasForServices($channelId, $serviceIds)
    {
        $usedCriteriasIds = $this->findAllUsedRatingCriteriaIds($channelId);
        $expr = $this->getEntityManager()->createQueryBuilder()->expr();

        $excludedCriteriasRule = null;
        if (!empty($usedCriteriasIds)) {
            $excludedCriteriasRule = $expr->notIn('e.id', $usedCriteriasIds);
        }
        $rule = $expr->andX($excludedCriteriasRule, $expr->in('stc.serviceType', $serviceIds));

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

    /**
     * @param $result
     * @return array
     */
    private function mergeToSingleArray($result)
    {
        $criteriaIds = array();
        foreach ($result as $singleId) {
            $criteriaIds = array_merge($criteriaIds, $singleId);
        }
        return $criteriaIds;
    }
}
