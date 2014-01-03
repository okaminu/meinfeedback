<?php
namespace MFB\ChannelBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ChannelCriteriaRepository extends EntityRepository
{
    public function findAllUnusedRatingCriterias($channelId)
    {
        $usedCriteriasIds = $this->findAllUsedRatingCriteriaIds($channelId);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $result = $qb->select('e')
            ->from('MFBRatingBundle:Rating', 'e')
            ->where(
                $qb->expr()->notIn('e.id', $usedCriteriasIds)
            )
            ->getQuery()
            ->getResult();
        return $result;
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
