<?php

namespace BlogBundle\Controller;

use BlogBundle\BlogBundle;
use BlogBundle\Entity\Category;
use BlogBundle\Entity\Tag;
use BlogBundle\Form\CategoryType;
use BlogBundle\Form\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class CategoryController extends Controller
{
    private $session;

    public  function __construct(){
        $this->session = new Session();
    }

    public function indexAction(){
        $em = $this->getDoctrine()->getManager();

        $categorias_repo = $em->getRepository("BlogBundle:Category");

        $categorias = $categorias_repo->findAll();


        return $this->render("BlogBundle:Category:index.html.twig",[
            "categorias" => $categorias
        ]);


    }
    public function addAction(Request $request){
        $category = new Category();
        $form = $this->createForm(CategoryType::class,$category);

        $form->handleRequest($request);//recogemos lo que llega del formulario

        if($form->isSubmitted()){
            if($form->isValid()){

                $em = $this->getDoctrine()->getManager();

                $category = new Category();
                $category->setName($form->get("name")->getData());
                $category->setDescription($form->get("description")->getData());

                $em->persist($category);

                $flush  = $em->flush();

                if($flush == null){
                    $status = "La categoria se ha creado correctamente";
                    $class_alert = "success";
                }
                else{
                    $status = "La categoria no se ha creado correctamente";
                    $class_alert = "danger";
                }


            }
            else{
                $status = "La categoria no se ha creado correctamente, porque el formulario no es válido";
                $class_alert = "danger";
            }

            //mensajes de respuesta
            $this->session->getFlashBag()->add("status",$status);
            $this->session->getFlashBag()->add("class_alert",$class_alert);
            return $this->redirectToRoute("blog_index_categoria");
        }



        return $this->render("BlogBundle:Category:add.html.twig",[
            "form" => $form->createView()
        ]);
    }

    public function deleteAction($id){
        if($id){
            $em = $this->getDoctrine()->getManager();
            $categoria_repo = $em->getRepository("BlogBundle:Category");
            $categoria = $categoria_repo->find($id);
            $em->remove($categoria);
            $em->flush();
            return $this->redirectToRoute("blog_index_categoria");
        }

    }

    public function editAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $categoria_repo = $em->getRepository("BlogBundle:Category");
        $categoria = $categoria_repo->find($id);

        $form = $this->createForm(CategoryType::class,$categoria);
        $form->handleRequest($request);//recogemos lo que llega del formulario

        if($form->isSubmitted()){
            if($form->isValid()){

                $categoria->setName($form->get("name")->getData());
                $categoria->setDescription($form->get("description")->getData());

                $em->persist($categoria);

                $flush  = $em->flush();

                if($flush == null){
                    $status = "La categoria se ha actualizado correctamente";
                    $class_alert = "success";
                }
                else{
                    $status = "La categoria no se ha actualizado correctamente";
                    $class_alert = "danger";
                }


            }
            else{
                $status = "La categoria no se ha actualizado correctamente, porque el formulario no es válido";
                $class_alert = "danger";
            }

            //mensajes de respuesta
            $this->session->getFlashBag()->add("status",$status);
            $this->session->getFlashBag()->add("class_alert",$class_alert);
            return $this->redirectToRoute("blog_index_categoria");
        }

        return $this->render("BlogBundle:Category:edit.html.twig",[
            "form" => $form->createView()
        ]);


    }
}
