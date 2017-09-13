<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="products")
 * @ORM\Entity
 */
class Product
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=1)
     */
    private $item_name;
    

    /**
     * @ORM\Column(type="string", length=250)
     */
    private $item_description;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $item_size;

    /**
     * @ORM\Column(type="float")
     */
    private $item_cost;

    public function __construct()
    {
        
    }
    
    public function setItemName($item_name){
        $this->item_name = $item_name;
    }
    
    public function getItemName()
    {
        return $this->item_name;
    }     
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }     

    public function setItemDescription($item_description){
        $this->item_description = $item_description;
    }
    
    public function getItemDescription()
    {
        return $this->item_description;
    }
    
    public function setItemSize($item_size){
        $this->item_size = $item_size;
    }
    
    public function getItemSize()
    {
        return $this->item_size;
    }
    
    public function setItemCost($item_cost){
        $this->item_cost = $item_cost;
    }

    public function getItemCost()
    {
        return $this->item_cost;
    }
}