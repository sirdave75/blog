<?php

namespace BlogBundle\Controller;

use BlogBundle\BlogBundle;
use BlogBundle\Entity\Tag;
use BlogBundle\Form\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class TagController extends Controller
{
    private $session;

    public  function __construct(){
        $this->session = new Session();
    }

    public function indexAction(){
        $em = $this->getDoctrine()->getManager();

        $tag_repo = $em->getRepository("BlogBundle:Tag");

        $tags = $tag_repo->findAll();


        return $this->render("BlogBundle:Tag:index.html.twig",[
            "tags" => $tags
        ]);


    }
    public function addAction(Request $request){
        $tag = new Tag();
        $form = $this->createForm(TagType::class,$tag);

        $form->handleRequest($request);//recogemos lo que llega del formulario

        if($form->isSubmitted()){
            if($form->isValid()){

                $em = $this->getDoctrine()->getManager();

                $tag = new Tag();
                $tag->setName($form->get("name")->getData());
                $tag->setDescription($form->get("description")->getData());

                $em->persist($tag);

                $flush  = $em->flush();

                if($flush == null){
                    $status = "La etiqueta se ha creado correctamente";
                    $class_alert = "success";
                }
                else{
                    $status = "La etiqueta no se ha creado correctamente";
                    $class_alert = "danger";
                }


            }
            else{
                $status = "La etiqueta no se ha creado correctamente, porque el formulario no es válido";
                $class_alert = "danger";
            }

            //mensajes de respuesta
            $this->session->getFlashBag()->add("status",$status);
            $this->session->getFlashBag()->add("class_alert",$class_alert);
            return $this->redirectToRoute("blog_index_tag");
        }



        return $this->render("BlogBundle:Tag:add.html.twig",[
            "form" => $form->createView()
        ]);
    }

    public function deleteAction($id){
        if($id){
            $em = $this->getDoctrine()->getManager();
            $tag_repo = $em->getRepository("BlogBundle:Tag");
            $tag = $tag_repo->find($id);
            $em->remove($tag);
            $em->flush();
            return $this->redirectToRoute("blog_index_tag");
        }

    }
    public function editAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $tag_repo = $em->getRepository("BlogBundle:Tag");
        $tag = $tag_repo->find($id);

        $form = $this->createForm(TagType::class,$tag);
        $form->handleRequest($request);//recogemos lo que llega del formulario

        if($form->isSubmitted()){
            if($form->isValid()){

                $tag->setName($form->get("name")->getData());
                $tag->setDescription($form->get("description")->getData());

                $em->persist($tag);

                $flush  = $em->flush();

                if($flush == null){
                    $status = "La Etiqueta se ha actualizado correctamente";
                    $class_alert = "success";
                }
                else{
                    $status = "La Etiqueta no se ha actualizado correctamente";
                    $class_alert = "danger";
                }


            }
            else{
                $status = "La Etiqueta no se ha actualizado correctamente, porque el formulario no es válido";
                $class_alert = "danger";
            }

            //mensajes de respuesta
            $this->session->getFlashBag()->add("status",$status);
            $this->session->getFlashBag()->add("class_alert",$class_alert);
            return $this->redirectToRoute("blog_index_tag");
        }

        return $this->render("BlogBundle:Tag:edit.html.twig",[
            "form" => $form->createView()
        ]);


    }
}
