<?php

namespace BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,["label"=>"Nombre:","required"=>"required", "attr"=>["class" => "form-name form-control col-sm-8"]])
            ->add('surname', TextType::class,["label"=>"Apellidos:","required"=>"required", "attr"=>["class" => "form-surname form-control col-sm-8"]])
            ->add('email', EmailType::class,["label"=>"Email:","required"=>"required", "attr"=>["class" => "form-email form-control col-sm-8"]])
            ->add('password', PasswordType::class,["label"=>"Password:","required"=>"required", "attr"=>["class" => "form-password form-control col-sm-8"]])
            ->add('Guardar', SubmitType::class, ["attr"=>["class"=>"btn btn-success ", "style"=>"margin-top:20px;"]]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BlogBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'blogbundle_user';
    }


}
