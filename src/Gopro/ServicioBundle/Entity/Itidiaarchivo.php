<?php

namespace Gopro\ServicioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gopro\CotizacionBundle\GoproCotizacionBundle;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;

use Gopro\MainBundle\Traits\ArchivoTrait;


/**
 * Itidiaarchivo
 *
 * @ORM\Table(name="ser_itidiaarchivo")
 * @ORM\Entity(repositoryClass="Gopro\ServicioBundle\Repository\ItidiaarchivoRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="Gopro\ServicioBundle\Entity\ItidiaarchivoTranslation")
 */
class Itidiaarchivo implements TranslatableInterface
{
    use PersonalTranslatableTrait;

    use ArchivoTrait;

    private $path = '/carga/goprocotizacion/itidiaarchivo';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255)
     */
    private $titulo;

    /**
     * @var \Gopro\ServicioBundle\Entity\Itinerariodia
     *
     * @ORM\ManyToOne(targetEntity="Gopro\ServicioBundle\Entity\Itinerariodia", inversedBy="itidiaarchivos")
     * @ORM\JoinColumn(name="itinerariodia_id", referencedColumnName="id", nullable=false)
     */
    protected $itinerariodia;

    /**
     * @var \DateTime $creado
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $creado;

    /**
     * @var \DateTime $modificado
     *
     * @ORM\Column(type="datetime")
     */
    private $modificado;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getNombre() ?? sprintf("Id: %s.", $this->getId()) ?? '';
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
     * Set itinerariodia
     *
     * @param \Gopro\ServicioBundle\Entity\Itinerariodia $itinerariodia
     *
     * @return Itidiaarchivo
     */
    public function setItinerariodia(\Gopro\ServicioBundle\Entity\Itinerariodia $itinerariodia = null)
    {
        $this->itinerariodia = $itinerariodia;

        return $this;
    }

    /**
     * Get itinerariodia
     *
     * @return \Gopro\ServicioBundle\Entity\Itinerariodia
     */
    public function getItinerariodia()
    {
        return $this->itinerariodia;
    }


    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Itidiaarchivo
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
     * @return Itidiaarchivo
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
     * Set titulo.
     *
     * @param string $titulo
     *
     * @return Itidiaarchivo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    
        return $this;
    }

    /**
     * Get titulo.
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

}
