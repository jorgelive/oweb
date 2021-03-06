<?php

namespace Gopro\ServicioBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\TranslationBundle\Filter\TranslationFieldFilter;

class ItinerariodiaAdmin extends AbstractAdmin
{

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('itinerario')
            ->add('dia')
            ->add('titulo', TranslationFieldFilter::class, [
                'label' => 'Título'
            ])
            ->add('importante')
            ->add('contenido', TranslationFieldFilter::class)
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('itinerario')
            ->add('dia')
            ->add('titulo', null, [
                'label' => 'Título'
            ])
            ->add('importante')
            ->add('contenido')
            ->add('_action', null, [
                'label' => 'Acciones',
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->getRoot()->getClass() != 'Gopro\ServicioBundle\Entity\Servicio'
            && $this->getRoot()->getClass() != 'Gopro\ServicioBundle\Entity\Itinerario'
        ){
            $formMapper->add('itinerario');
        }
        $formMapper
            ->add('dia')
            ->add('titulo', null, [
                'label' => 'Título'
            ])
            ->add('importante')
            ->add('contenido', null, [
                'required' => false,
                'attr' => ['class' => 'ckeditor']
            ])
            ->add('itidiaarchivos', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Archivos'
            ], [
                'edit' => 'inline',
                'inline' => 'table'
            ])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('itinerario')
            ->add('dia')
            ->add('titulo', null, [
                'label' => 'Título'
            ])
            ->add('importante')
            ->add('contenido')
        ;
    }
}
