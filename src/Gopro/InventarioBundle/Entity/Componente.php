<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Componente
 *
 * @ORM\Table(name="inv_componente")
 * @ORM\Entity
 */
class Componente
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var datetime $fechacompra
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechacompra;

    /**
     * @var datetime $fechafingarantia
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechafingarantia;

    /**
     * @var datetime $fechafingarantia
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechabaja;

    /**
     * @var datetime $creado
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $creado;

    /**
     * @var datetime $modificado
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $modificado;

    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="componentes")
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="Componentetipo", inversedBy="componentes")
     */
    private $componentetipo;

    /**
     * @ORM\ManyToOne(targetEntity="Componenteestado", inversedBy="componentes")
     */
    private $componenteestado;

    /**
     * @ORM\OneToMany(targetEntity="Componentecaracteristica", mappedBy="componente", cascade={"persist","remove"})
     */
    private $componentecaracteristicas;

    public function __construct() {
        $this->componentecaracteristicas = new ArrayCollection();
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getComponentetipo()->getNombre();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechacompra
     *
     * @param \DateTime $fechacompra
     * @return Componente
     */
    public function setFechacompra($fechacompra)
    {
        $this->fechacompra = $fechacompra;

        return $this;
    }

    /**
     * Get fechacompra
     *
     * @return \DateTime 
     */
    public function getFechacompra()
    {
        return $this->fechacompra;
    }

    /**
     * Set fechafingarantia
     *
     * @param \DateTime $fechafingarantia
     * @return Componente
     */
    public function setFechafingarantia($fechafingarantia)
    {
        $this->fechafingarantia = $fechafingarantia;

        return $this;
    }

    /**
     * Get fechafingarantia
     *
     * @return \DateTime 
     */
    public function getFechafingarantia()
    {
        return $this->fechafingarantia;
    }


    /**
     * Set fechabaja
     *
     * @param \DateTime $fechabaja
     * @return Componente
     */
    public function setFechabaja($fechabaja)
    {
        $this->fechabaja = $fechabaja;

        return $this;
    }

    /**
     * Get fechabaja
     *
     * @return \DateTime
     */
    public function getFechabaja()
    {
        return $this->fechabaja;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Componente
     */
    public function setCreado($creado)
    {
        $this->creado = $creado;

        return $this;
    }

    /**
     * Get creado
     *
     * @return \DateTime 
     */
    public function getCreado()
    {
        return $this->creado;
    }

    /**
     * Set modificado
     *
     * @param \DateTime $modificado
     * @return Componente
     */
    public function setModificado($modificado)
    {
        $this->modificado = $modificado;

        return $this;
    }

    /**
     * Get modificado
     *
     * @return \DateTime 
     */
    public function getModificado()
    {
        return $this->modificado;
    }

    /**
     * Set item
     *
     * @param \Gopro\InventarioBundle\Entity\Item $item
     * @return Componente
     */
    public function setItem(\Gopro\InventarioBundle\Entity\Item $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \Gopro\InventarioBundle\Entity\Item 
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set componentetipo
     *
     * @param \Gopro\InventarioBundle\Entity\Componentetipo $componentetipo
     * @return Componente
     */
    public function setComponentetipo(\Gopro\InventarioBundle\Entity\Componentetipo $componentetipo = null)
    {
        $this->componentetipo = $componentetipo;

        return $this;
    }

    /**
     * Get componentetipo
     *
     * @return \Gopro\InventarioBundle\Entity\Componentetipo 
     */
    public function getComponentetipo()
    {
        return $this->componentetipo;
    }

    /**
     * Set componenteestado
     *
     * @param \Gopro\InventarioBundle\Entity\Componenteestado $componenteestado
     * @return Componente
     */
    public function setComponenteestado(\Gopro\InventarioBundle\Entity\Componenteestado $componenteestado = null)
    {
        $this->componenteestado = $componenteestado;

        return $this;
    }

    /**
     * Get componenteestado
     *
     * @return \Gopro\InventarioBundle\Entity\Componenteestado 
     */
    public function getComponenteestado()
    {
        return $this->componenteestado;
    }

    /**
     * Add componentecaracteristicas
     *
     * @param \Gopro\InventarioBundle\Entity\Componentecaracteristica $componentecaracteristicas
     * @return Componente
     */
    public function addComponentecaracteristica(\Gopro\InventarioBundle\Entity\Componentecaracteristica $componentecaracteristicas)
    {
        $this->componentecaracteristicas[] = $componentecaracteristicas;

        return $this;
    }

    /**
     * Remove componentecaracteristicas
     *
     * @param \Gopro\InventarioBundle\Entity\Componentecaracteristica $componentecaracteristicas
     */
    public function removeComponentecaracteristica(\Gopro\InventarioBundle\Entity\Componentecaracteristica $componentecaracteristicas)
    {
        $this->componentecaracteristicas->removeElement($componentecaracteristicas);
    }

    /**
     * Get componentecaracteristicas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComponentecaracteristicas()
    {
        return $this->componentecaracteristicas;
    }

}