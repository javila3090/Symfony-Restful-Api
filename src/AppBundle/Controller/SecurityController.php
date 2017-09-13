<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\UserType;
use AppBundle\Entity\User;


class SecurityController extends Controller
{
    /**
     * @Route("/", name="loginpage")
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();   
 
        //Llamamos al servicio de autenticacion
        $authenticationUtils = $this->get('security.authentication_utils');

        // conseguir el error del login si falla
        $error = $authenticationUtils->getLastAuthenticationError();

        // ultimo nombre de usuario que se ha intentado identificar
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render(
            'Api/login.html.twig',
            array(                
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }
    
    public function registerAction(Request $request)
    {
        
        $session = $request->getSession();
            
        $user = new User();
           
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
            
        if ($form->isSubmitted()) {
            
            $name = $form->get('name')->getData();
            $username = $form->get('username')->getData();
            $email = $form->get('email')->getData();            

            //Cifra la contraseña
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($form->get('password')->getData(), $user->getSalt());

            //Seteamos los atributos
            $user->setUsername($name);
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($password);
        
            if ($form->isValid()) {
                try{
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    
                    //Enviar mail
                    $message = (new \Swift_Message('Welcome Email'))
                    ->setFrom('admin@symfony.com')
                    ->setTo($name)
                    ->setBody(
                        $this->renderView(
                            'Blog/Emails/welcome.html.twig',
                            array('name' => $name)
                        ),
                        'text/html'
                    );

                    $this->get('mailer')->send($message);
                    
                    //generar flasdata
                    $session->getFlashBag()->add('info', '¡Registro realizado con éxito!');

                    return $this->redirectToRoute('registro_form');

                } catch(\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $session->getFlashBag()->add('error', '¡Aviso! Ocurrió un error al registrar');
                    return $this->redirect($this->generateUrl('registro_form'));
                }
            }
        }
        return $this->render(
            'Blog/registro.html.twig',
            array('form' => $form->createView())
        );
    }
}
