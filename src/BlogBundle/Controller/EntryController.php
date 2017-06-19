<?php

namespace BlogBundle\Controller;


use BlogBundle\BlogBundle;
use BlogBundle\Entity\Entry;
use BlogBundle\Form\EntryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class EntryController extends Controller
{
    private $session;

    public  function __construct(){
        $this->session = new Session();
    }

    public function indexAction($page){

        $em = $this->getDoctrine()->getManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $category_repo = $em->getRepository("BlogBundle:Category");

        $pageSize = 1;
        $entries = $entry_repo->getPaginateEntries($pageSize,$page);
        $categories = $category_repo->findAll();

        $totalItems = Count($entries);
        $pagesCount = ceil($totalItems/$pageSize);
        return $this->render("BlogBundle:Entry:index.html.twig",[
            "entries"=>$entries,
            "categories"=>$categories,
            "totalItems"=>$totalItems,
            "pagesCount"=>$pagesCount,
            "page" => $page
        ]);
    }

    public function addAction(Request $request)
    {
        $entry = new Entry();
        $form = $this->createForm(EntryType::class, $entry);

        $form->handleRequest($request);//recogemos lo que llega del formulario

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $category_repo = $em->getRepository("BlogBundle:Category");

                $entry_repo = $em ->getRepository("BlogBundle:Entry");

                $entry = new Entry();
                $entry->setTitle($form->get("title")->getData());
                $entry->setContent($form->get("content")->getData());
                $entry->setStatus($form->get("status")->getData());


                //upload file
                $file = $form["image"]->getData(); // es lo mismo que $form->get("image")->getData();
                $ext = $file->guessExtension();
                $file_name = time().".".$ext;
                $file->move("uploads",$file_name);

                $entry->setImage($file_name);

                $category = $category_repo->find($form->get("category")->getData());
                $entry->setCategory($category);

                $user = $this->getUser();

                $entry->setUser($user);

                $em->persist($entry);

                $flush  = $em->flush();

                $entry_repo->saveEntryTags(
                    $form->get("tags")->getData(),
                    $entry,
                    $category,
                    $user
                );
                if($flush == null){
                    $status = "La entrada se ha creado correctamente";
                    $class_alert = "success";
                }
                else{
                    $status = "La entrada no se ha creado correctamente";
                    $class_alert = "danger";
                }


            }
            else{
                $status = "La entrada no se ha creado correctamente, porque el formulario no es válido";
                $class_alert = "danger";
            }

            //mensajes de respuesta
           $this->session->getFlashBag()->add("status",$status);
           $this->session->getFlashBag()->add("class_alert",$class_alert);
           return $this->redirectToRoute("blog_homepage");
        }
        return $this->render(
            "BlogBundle:Entry:add.html.twig",
            [
                "form" => $form->createView()
            ]
        );

    }

    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");

        $entry = $entry_repo->find($id);
        $em -> remove($entry);

        $em->flush();
        return $this->redirectToRoute("blog_homepage");
    }

    public  function  editAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $category_repo = $em->getRepository("BlogBundle:Category");
        $entry = $entry_repo->find($id);

        $tags = '';
        foreach ($entry->getEntryTag() as $entryTag){
            $tags .= $entryTag->getTag()->getName().', ';
        }
        $tags = substr(trim($tags),0,-1);

        $file_name = $entry->getImage();
        $form = $this->createForm(EntryType::class,$entry);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form->isValid()){


                $entry->setTitle($form->get("title")->getData());
                $entry->setContent($form->get("content")->getData());
                $entry->setStatus($form->get("status")->getData());


                //upload file
                $file = $form["image"]->getData(); // es lo mismo que $form->get("image")->getData();

                if($file != null) {
                    $ext = $file->guessExtension();
                    $file_name = time().".".$ext;
                    $file->move("uploads", $file_name);

                    $entry->setImage($file_name);
                }
                else {
                    $entry->setImage($file_name);

                }
                $category = $category_repo->find($form->get("category")->getData());
                $entry->setCategory($category);

                $user = $this->getUser();

                $entry->setUser($user);

                $em->persist($entry);

                $flush  = $em->flush();

                $entry_tag_repo = $em ->getRepository("BlogBundle:EntryTag");
                $entry_tags = $entry_tag_repo->findBy(["entry"=>$entry]);
                foreach($entry_tags as $entryTag){
                    if(is_object($entryTag)) {
                        $em->remove($entryTag);
                        $em->flush();
                    }
                }

                $entry_repo->saveEntryTags(
                    $form->get("tags")->getData(),
                    $entry,
                    $category,
                    $user
                );

                if($flush == null){
                    $status = "La entrada se ha modificado correctamente";
                    $class_alert = "success";
                }
                else{
                    $status = "La entrada no se ha modificado correctamente";
                    $class_alert = "danger";
                }
            }
            else{
                $status = "La entrada no se ha creado correctamente, porque el formulario no es válido";
                $class_alert = "danger";
            }

            //mensajes de respuesta
            $this->session->getFlashBag()->add("status",$status);
            $this->session->getFlashBag()->add("class_alert",$class_alert);
            return $this->redirectToRoute("blog_homepage");
        }
        return $this->render(
            "BlogBundle:Entry:edit.html.twig",
            [
                "form" => $form->createView(),
                "entry" => $entry,
                "tags" => $tags
            ]
        );

    }

}
