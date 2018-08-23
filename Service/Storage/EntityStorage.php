<?php
namespace EMS\CoreBundle\Service\Storage;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use EMS\CoreBundle\Entity\AssetStorage;
use EMS\CoreBundle\Repository\AssetStorageRepository;
use Exception;
use function file_get_contents;
use function filemtime;
use function filesize;
use function unlink;

class EntityStorage implements StorageInterface {

    /**@var Registry $doctrine */
    private $doctrine;
    /**@var ObjectManager*/
    private $manager;
    /**@var AssetStorageRepository*/
    private $repository;
    /**@var bool */
    private $contextSupport;

    /**
     * EntityStorage constructor.
     * @param Registry $doctrine
     * @param bool $contextSupport
     */
	public function __construct(Registry $doctrine, bool $contextSupport) {
        $this->doctrine = $doctrine;
        $this->contextSupport = $contextSupport;
        $this->repository = false;
	}

    /**
     *
     */
	private function init()
    {
        if($this->repository === false)
        {
            $this->manager = $this->doctrine->getManager();
            $this->repository = $this->manager->getRepository('EMSCoreBundle:AssetStorage');
        }
    }

    /**
     * @return bool
     */
	public function supportCacheStore()
    {
        return $this->contextSupport;
    }

    /**
     * @param string $hash
     * @param bool|string $cacheContext
     * @return bool
     */
    public function head($hash, $cacheContext=false) {
        if($cacheContext === false || $this->contextSupport)
        {
            $this->init();
            return $this->repository->head($hash, $cacheContext);
        }
        return false;
    }

    /**
     * @param string $hash
     * @param bool|string $cacheContext
     * @return bool|int
     */
    public function getSize($hash, $cacheContext=false) {
        if($cacheContext === false || $this->contextSupport)
        {
            $this->init();
            return $this->repository->getSize($hash, $cacheContext);
        }
        return false;
    }

    /**
     * @param string $hash
     * @param string $filename
     * @param bool|string $cacheContext
     * @return bool
     */
	public function create($hash, $filename, $cacheContext=false){
        if($cacheContext === false || $this->contextSupport)
        {
            $this->init();
            $entity = new AssetStorage();
            $entity->setLastUpdateDate(filemtime($filename));
            $entity->setHash($hash);
            $entity->setSize(filesize($filename));
            $entity->setContents(file_get_contents($filename));
            $entity->setContext($cacheContext?$cacheContext:null);
            $this->manager->persist($entity);
            $this->manager->flush($entity);

            return true;
        }
		return false;
	}

    /**
     * @param string $hash
     * @param bool|string $cacheContext
     * @return bool|resource
     */
	public function read($hash, $cacheContext=false){
        if($cacheContext === false || $this->contextSupport)
        {
            $this->init();
            /**@var AssetStorage $entity*/
            $entity = $this->repository->findByHash($hash, $cacheContext);
            if($entity)
            {
                return $entity->getContents();
            }
        }
        return false;
	}

    /**
     * @param string $hash
     * @param bool|string $cacheContext
     * @return bool|int
     */
	public function getLastUpdateDate($hash, $cacheContext=false){
        if($cacheContext === false || $this->contextSupport)
        {
            $this->init();
            return $this->repository->head($hash, $cacheContext);
        }
        return false;
	}

    public function __toString()
    {
        return EntityStorage::class;
    }

    /**
     * @return bool
     */
    public function clearCache()
    {
        $this->init();
        return $this->repository->clearCache();
    }
}
