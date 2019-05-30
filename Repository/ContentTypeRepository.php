<?php

namespace EMS\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use EMS\CoreBundle\Entity\ContentType;

/**
 * ContentTypeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContentTypeRepository extends EntityRepository
{
    
    public function findAllAsAssociativeArray()
    {
        $qb = $this->createQueryBuilder('ct');
        $qb->where($qb->expr()->eq('ct.deleted', ':false'));
        $qb->setParameters([
            'false' => false,
        ]);
        
        $out = [];
        $result = $qb->getQuery()->getResult();
        /** @var ContentType $record */
        foreach ($result as $record) {
            $out[$record->getName()] = $record;
        }
        
        return $out;
    }

    /**
     * @return ContentType[]
     */
    public function findAll()
    {
        return parent::findBy(['deleted' => false], ['orderKey' => 'ASC']);
    }

    /**
     *
     * @param string $name
     * @return ContentType|null
     */
    public function findByName($name)
    {
        /** @var ContentType|null $contentType */
        $contentType = $this->findOneBy([
            'deleted' => false,
            'name' => $name,
        ]);

        return $contentType;
    }

    /**
     * @return int
     * @throws NonUniqueResultException
     */
    public function countContentType() : int
    {
        return $this->createQueryBuilder('a')
         ->select('COUNT(a)')
         ->getQuery()
         ->getSingleScalarResult();
    }


    /**
     * @return int
     * @throws NonUniqueResultException
     */
    public function maxOrderKey() : int
    {
        return $this->createQueryBuilder('a')
         ->select('max(a.orderKey)')
         ->getQuery()
         ->getSingleScalarResult();
    }
}
