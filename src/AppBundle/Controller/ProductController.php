<?php

namespace AppBundle\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use Unirest\Request as Unirest;


class ProductController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual item
     *
     * @param int $id
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Product",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function getAction($id)
    {
        return $this->getDoctrine()->getRepository('AppBundle:Product')->find($id);
    }
    
     /**
     * @Route("/products", name="get_products")
     *
     */
    public function getListAction()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Product')->findAll();
    }  
    
     /**
     * Gets an individual item
     *
     * @param Request $request
     *
     * @ApiDoc(
      *    input="AppBundle\Form\Type\ProductType",
     *     output="AppBundle\Entity\Product",
     *     statusCodes={
     *         201 = "Returned when a new register has been created",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(ProductType::class, null,[
            'csrf_protection' => false,
        ]);
        
        $form ->submit($request->request->all());
        
        if(!$form->isValid()){
            return $form;
        }
        
        $product = $form->getData();
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        
        $routeOptions = [
            'id' => $product->getId(),
            '_format' => $request->get('_format'),
        ];
        
        return $this->routeRedirectView('CREATED SUCCESSFULLY', $routeOptions, Response::HTTP_CREATED);
    }    
    
    public function putAction(Request $request, $id)
    {
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        //Repositorios de entidades a utilizar
        $productRepository=$em->getRepository("AppBundle:Product");
        
        //conseguimos el objeto del Post
        $product = $productRepository->find($id);

        if ($product === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ProductType::class, $product, [
            'csrf_protection' => false,
        ]);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $routeOptions = [
            'id' => $product->getId(),
            '_format' => $request->get('_format'),
        ];

        return $this->routeRedirectView("UPDATED SUCCESSFULLY", $routeOptions, Response::HTTP_NO_CONTENT);
    }    
    
     /**
     * Delete an individual item
     *
     * @param int $id
     * @return View
     *
     * @ApiDoc(
     *     statusCodes={
     *         202 = "Returned when a register has been deleted",
     *         404 = "Return when not found"
     *     }
     * )
     */
    
    public function deleteAction($id)
    {
        /**
         * @var $product Product
         */
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        //Repositorios de entidades a utilizar
        $productRepository=$em->getRepository("AppBundle:Product");
        
        //conseguimos el objeto del Post
        $product = $productRepository->find($id);        

        if ($product === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        return new View("DELETED SUCCESSFULLY", Response::HTTP_NO_CONTENT);
    }  
    
    /**
     * @Route("/products/list/", name="list_products")
     * @Security("is_granted('ROLE_USER')")
     */
    
    public function listProductsAction()
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://symfonyapi.dev/app_dev.php/products');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        $data = json_decode($response);
        return $this->render('Api/list.html.twig',array('products' => $data));
    }
    
    /**
     * @Route("/products/new/", name="new_product")
     * @Security("is_granted('ROLE_USER')")
     */    
    
    public function createProductAction(Request $request)
    {
        $session = $request->getSession();
            
        $product = new Product();
           
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
            
        if ($form->isSubmitted() && $form->isValid()) {
            
            //Obteniendo el id del usuario logueado
            $logUser = $this->get('security.token_storage')->getToken()->getUser();
            $userId = $logUser->getId();
            $user = $this->getDoctrine()
               ->getManager()
               ->getRepository('AppBundle:User')
               ->findOneById($userId);           
            
            //Obteniendo datos desde el formulario
            $item_name = $form->get('item_name')->getData();
            $item_description = $form->get('item_description')->getData();
            $item_size = $form->get('item_size')->getData();
            $item_cost = $form->get('item_cost')->getData();

            //Seteando los atributos
            $product->setItemName($item_name);
            $product->setItemDescription($item_description);
            $product->setItemSize($item_size);
            $product->setItemCost($item_cost);     
        
            if ($form->isValid()) {
                try{
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($product);
                    $em->flush();

                    //generar flasdata
                    $session->getFlashBag()->add('info', 'Successful!');

                    return $this->redirectToRoute('new_product');

                } catch(\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $session->getFlashBag()->add('error', $errorMessage);
                    return $this->redirect($this->generateUrl('new_product'));
                }
            }
        }
        return $this->render(
            'Api/new.html.twig',
            array('form' => $form->createView())
        );        
    } 
    
    /**
     * @Route("/products/edit/{id}", name="edit_product")
     * @Security("is_granted('ROLE_USER')")
     */    
        
    public function editAction(Request $request,$id){
        
        $session = $request->getSession();
        
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        //Repository
        $productRepository=$em->getRepository("AppBundle:Product");
        
        $product = $productRepository->findOneBy(array("id"=>$id));

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
                       
            $productId = $form->get('id')->getData();
            $category = $this->getDoctrine()
               ->getManager()
               ->getRepository('AppBundle:Product')
               ->findOneById($productId);            
            
            //Getting objects
            $item_name = $form->get('item_name')->getData();
            $item_description = $form->get('item_description')->getData();
            $item_size = $form->get('item_size')->getData();
            $item_cost = $form->get('item_cost')->getData();
            
            //Setting attr
            $product->setItemName($item_name);
            $product->setItemDescription($item_description);
            $product->setItemSize($item_size);
            $product->setItemCost($item_cost);
            
        }
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
  
            //Flash
            $session->getFlashBag()->add('info', '¡Successful!');
            
            return $this->redirect($this->generateURL('list_products'));
        }else{
            if ($form->isSubmitted()) {
                
                //Flash
                $session->getFlashBag()->add('info', 'Error! There are empty fields');
            }
        }
        
        //Render view
        return $this->render(
            'Api/edit.html.twig',
            array('form' => $form->createView())
        );
    }
    
    public function deleteProductAction(Request $request,$id){
        
        $session = $request->getSession();
        
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        //Repository
        $productRepository=$em->getRepository("AppBundle:Product");
        
        //Object
        $product = $productRepository->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();
        
        //flash
        $session->getFlashBag()->add('info', '¡Successful!');
            
        return $this->redirect($this->generateURL('list_products'));     
    }    
}
