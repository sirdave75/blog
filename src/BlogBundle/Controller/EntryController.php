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


    public function addAction(Request $request)
    {
        $entry = new Entry();
        $form = $this->createForm(EntryType::class, $entry);

        $form->handleRequest($request);//recogemos lo que llega del formulario

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $category_repo = $em->getRepository("BlogBundle:Category");
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
           //return $this->redirectToRoute("blog_index_entry");
        }
        return $this->render(
            "BlogBundle:Entry:add.html.twig",
            [
                "form" => $form->createView()
            ]
        );

    }


}
