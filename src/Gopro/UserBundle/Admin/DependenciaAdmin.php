<?php

namespace Gopro\UserBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Show\ShowMapper;

class DependenciaAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('organizacion', null, [
                'label' => 'Organización'
            ])
            ->add('nombre')
            ->add('email', null, [
                'label' => 'E-mail'
            ])
            ->add('direccion', null, [
                'label' => 'Dirección'
            ])
            ->add('color')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('organizacion', null, [
                'label' => 'Organización',
                'sortable' => true,
                'sort_field_mapping' => ['fieldName' => 'nombre'],
                'sort_parent_association_mappings' => [['fieldName' => 'organizacion']]
            ])
            ->add('nombre', null, [
                'editable' => true
            ])
            ->add('email', null, [
                'label' => 'E-mail',
                'editable' => true
            ])
            ->add('direccion', null, [
                'label' => 'Dirección',
                'editable' => true
            ])
            ->add('color', null, [
                'editable' => true
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
                'label' => 'Acciones'
            ])
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->getRoot()->getClass() != 'Gopro\UserBundle\Entity\Organizacion'){
            $formMapper
                ->add('organizacion', null, [
                    'required' => true,
                    'label' => 'Organización'
                ]);
        }

        $formMapper
            ->add('nombre')
            ->add('email', null, [
                'label' => 'E-mail'
            ])
            ->add('direccion', null, [
                'label' => 'Dirección'
            ])
            ->add('color')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('organizacion', null, [
                'label' => 'Organización'
            ])
            ->add('nombre')
            ->add('email', null, [
                'label' => 'E-mail'
            ])
            ->add('direccion', null, [
                'label' => 'Dirección'
            ])
            ->add('color')
        ;
    }
}
