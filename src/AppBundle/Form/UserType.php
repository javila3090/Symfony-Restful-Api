<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array("label" => "Name: ",
                    "required" => true,
                    "attr" => array('class' => 'form-control')))
                
                ->add('username', TextType::class, array("label" => "Username: ",
                    "required" => true,
                    "attr" => array('class' => 'form-control')))
                
                ->add('email', EmailType::class, array("label" => "Email: ",
                    "required" => true,
                    "attr" => array('class' => 'form-control')))
                
                ->add('password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'invalid_message' => 'Passwords must be same',
                    'required' => true,
                    'first_options' => array('label' => 'Password: ',"attr" => array('class' => 'form-control')),
                    'second_options' => array('label' => 'Repeat Password: ',"attr" => array('class' => 'form-control'))));
    }

    public function getName() {
        return 'Register';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}