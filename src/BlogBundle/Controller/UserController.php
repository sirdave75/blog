<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\User;
use BlogBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{
    private $session;

    public  function __construct(){
        $this->session = new Session();
    }
    public function loginAction(Request $request){

        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUserName = $authenticationUtils->getLastUsername();
        $user = new User();

        //creo el formulario
        $form = $this->createForm(UserType::class, $user);
        //paso los vaalores
        $form->handleRequest($request);

        //valido
        if($form->isSubmitted()){
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $user_repo = $em->getRepository("BlogBundle:User");
                $user= $user_repo->findOneBy(["email"=>$form->get("email")->getData()]);

                if(count($user)==0) {
                    $user = new User();
                    $user->setName($form->get("name")->getData());
                    $user->setSurname($form->get("surname")->getData());
                    $user->setEmail($form->get("email")->getData());

                    $factory = $this->get("security.encoder_factory");
                    $encoder = $factory->getEncoder($user);
                    $password = $encoder->encodePassword($form->get("password")->getData(), $user->getSalt());

                    $user->setPassword($password);
                    $user->setRole("ROLE_USER");
                    $user->setImagen("");
                    $user->setActivo("1");
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $flush = $em->flush();
                    if ($flush == null) {
                        $status = "El usuario se ha creado correctamente";
                        $class_alert = "success";
                    } else {
                        $status = "No te has registrado correctamente";
                        $class_alert = "danger";
                    }
                }
                else{
                    $status = "El usuario ya existe";
                    $class_alert = "danger";
                }

            }
            else{
                $status = "No te has registrado correctamente";
                $class_alert = "danger";
            }

            $this->session->getFlashBag()->add("status",$status);
            $this->session->getFlashBag()->add("class_alert",$class_alert);

        }

        return $this->render('BlogBundle:User:login.html.twig',[
            "error"         =>  $error,
            "lastUserName"  =>  $lastUserName,
            "form"          =>  $form->createView()
        ]);
    }
}
