<?php

namespace BlogBundle\Form;

use BlogBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class EntryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,[
                "label" => "Título",
                "attr" => [
                            "class" => "form-control"
                          ]
            ])
            ->add('content',TextareaType::class,[
                "label" => "Contenido",
                "attr" => [
                            "class" => "form-control"
                          ]
            ])
            ->add('status',ChoiceType::class,[
                "label" => "Estado",
                "choices" =>[
                        "Publicado" => "public",
                        "Privado" => "private"
                    ],
                "attr" => [
                            "class" => "form-control"
                          ]
            ])
            ->add('image',FileType::class,[
                "label" => "Imagen",
                "attr" => [
                            "class" => "form-control"
                          ]
            ])
            ->add('category',EntityType::class,[
                "label" => "Categorias",
                "class" => Category::class,
                "attr" => [
                            "class" => "form-control"
                          ]
            ])
            ->add('tags',TextType::class,[
                "mapped" => false,
                "label" => "Etiquetas",
                "attr" => [
                            "class" => "form-control"
                          ]
            ])
            ->add('Guardar',SubmitType::class,[
                "attr"=>[
                            "class"=>"btn btn-success"
                        ]
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BlogBundle\Entity\Entry'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'blogbundle_entry';
    }


}
