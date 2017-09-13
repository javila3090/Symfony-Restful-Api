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
use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;

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
        
        return $this->routeRedirectView('get_product', $routeOptions, Response::HTTP_CREATED);
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

        return $this->routeRedirectView('get_product', $routeOptions, Response::HTTP_NO_CONTENT);
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

        return new View(null, Response::HTTP_NO_CONTENT);
    }    
}
