<?php
/**
 * Created by PhpStorm.
 * User: binll
 * Date: 15/06/2017
 * Time: 11:58
 */

namespace BlogBundle\Repository;


use BlogBundle\BlogBundle;
use BlogBundle\Entity\EntryTag;
use Doctrine\ORM\EntityRepository;
use BlogBundle\Entity\Tag;
use Doctrine\ORM\Tools\Pagination\Paginator;

class EntryRepository extends EntityRepository{

    public function saveEntryTags($tags=null,$entry=null,$category=null,$user=null){

        $em = $this->getEntityManager();

        $tag_repo = $em->getRepository("BlogBundle:Tag");

        $tags = explode(',',$tags);

        foreach($tags as $tag){
            $isset_tag = $tag_repo->findOneBy(['name'=>trim($tag)]);

            if(count($isset_tag) == 0){
                $tag_obj = new Tag();

                $tag_obj->setName(trim($tag));
                $tag_obj->setDescription(trim($tag));
                $em -> persist($tag_obj);
                $em -> flush();
            }

            $tag = $tag_repo->findOneBy(['name'=>trim($tag)]);

            $entryTag = new EntryTag();

            $entryTag ->setEntry($entry);
            $entryTag ->setTag($tag);

            $em -> persist($entryTag);
        }
        $flush = $em -> flush();
        return $flush;
    }

    public function getPaginateEntries($pagesSize=5,$currentPage=1){
        //consuta con DQL
        $em = $this->getEntityManager();
        $dql = "SELECT e from BlogBundle\Entity\Entry e ORDER BY e.id DESC";
        $query = $em -> createQuery($dql)
                ->setFirstResult($pagesSize*($currentPage-1))
                ->setMaxResults($pagesSize);

        $paginator = new Paginator($query,$fecthJoinColllection = true);

        return $paginator;
    }
}