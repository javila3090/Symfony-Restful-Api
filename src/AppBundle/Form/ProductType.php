<?php

namespace AppBundle\Form;

use AppBundle\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Description of ProductType
 *
 * @author Julio
 */

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('item_name', TextType::class, array("label" => "Name* ",
                    "required" => true,
                    "attr" => array('class' => 'form-control')))      
                
                ->add('item_size', TextType::class, array("label" => "Size* ",
                    "required" => true,
                    "attr" => array('class' => 'form-control')))  
                
                ->add('item_cost', TextType::class, array("label" => "Cost* ",
                    "required" => true,
                    "attr" => array('class' => 'form-control')))                  
                
                ->add('item_description', TextareaType::class, array("label" => "Description* ",
                    "required" => true,
                    "attr" => array('class' => 'form-control','rowspan' => '3')));         
    }

    public function getName() {
        return 'New_product';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Product::class,
        ));
    }
}