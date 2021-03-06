<?php

namespace EMS\CoreBundle\Repository;

use Doctrine\ORM\NoResultException;
use EMS\CoreBundle\Entity\ContentType;
use EMS\CoreBundle\Entity\Environment;
use EMS\CoreBundle\Entity\SingleTypeIndex;

/**
 * SingleTypeIndexRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SingleTypeIndexRepository extends \Doctrine\ORM\EntityRepository
{

    public function getIndexName(ContentType $contentType, Environment $environment)
    {

        $qb = $this->createQueryBuilder('i')
            ->select('i');
        $qb->where($qb->expr()->eq('i.contentType', ':contentType'))
            ->andWhere($qb->expr()->eq('i.environment', ':environment'))
            ->setParameters([
                ':environment' => $environment,
                ':contentType' => $contentType,
            ]);

        $result = $qb->getQuery()->getSingleResult();
        return $result;
    }


    public function setIndexName(Environment $environment, ContentType $contentType, string $name)
    {

        $singleTypeIndex = false;
        try {
            $qb = $this->createQueryBuilder('i')
                ->select('i');
            $qb->where($qb->expr()->eq('i.contentType', ':contentType'))
                ->andWhere($qb->expr()->eq('i.environment', ':environment'))
                ->setParameters([
                    ':environment' => $environment,
                    ':contentType' => $contentType,
                ]);

            $singleTypeIndex = $qb->getQuery()->getSingleResult();
        } catch (NoResultException$e) {
            $singleTypeIndex = new SingleTypeIndex();
            $singleTypeIndex->setContentType($contentType)
                ->setEnvironment($environment);
        }
        $singleTypeIndex->setName($name);
        $em = $this->getEntityManager();
        $em->persist($singleTypeIndex);
        $em->flush($singleTypeIndex);
    }
}
