<?php

namespace Gopro\CuentaBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class PeriodoAdmin extends AbstractAdmin
{

    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'modificado',
    ];


    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('cuenta')
            ->add('fechainicio', null, [
                'label' => 'Inicio'
            ])
            ->add('fechafin', null, [
                'label' => 'Fin'
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('cuenta', null, [
                'sortable' => true,
                'sort_field_mapping' => array('fieldName' => 'nombre'),
                'sort_parent_association_mappings' => array(array('fieldName' => 'cuenta'))
            ])
            ->add('fechainicio', null, [
                'label' => 'Inicio'
            ])
            ->add('fechafin', null, [
                'label' => 'Fin'
            ])
            ->add('modificado',  null, [
                'label' => 'Modificación',
                'format' => 'Y/m/d H:i'

            ])
            ->add('_action', null, [
                'label' => 'Acciones',
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ]
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->getRoot()->getClass() != 'Gopro\CuentaBundle\Entity\Cuenta'){
            $formMapper->add('cuenta');
        }

        $formMapper
            ->add('fechainicio', DatePickerType::class, [
                'label' => 'Inicio',
                'dp_use_current' => true,
                'dp_show_today' => true,
                'format'=> 'yyyy/MM/dd',
                'attr' => [
                    'class' => 'fecha'
                ]
            ])
            ->add('fechafin', DatePickerType::class, [
                'required' => false,
                'label' => 'Fin',
                'dp_use_current' => true,
                'dp_show_today' => true,
                'format'=> 'yyyy/MM/dd',
                'attr' => [
                    'class' => 'fecha'
                ]
            ]);

        if ($this->getRoot()->getClass() != 'Gopro\CuentaBundle\Entity\Cuenta'){
            $formMapper
                ->add('movimientos', CollectionType::class, [
                    'by_reference' => false,
                    'label' => 'Movimientos'
                ], [
                    'edit' => 'inline',
                    'inline' => 'table'
                ]);
            }
        ;


        $editModifier = function (FormInterface $form) {
            $form
                ->add('cuenta', null, [
                    'disabled' => 'true'
                ])
            ;
        };

        $formBuilder = $formMapper->getFormBuilder();
        $formBuilder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($editModifier) {
                if ($this->isCurrentRoute('edit')) {
                    $editModifier($event->getForm());
                }
            }
        );
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('cuenta')
            ->add('fechainicio', null, [
                'label' => 'Inicio'
            ])
            ->add('fechafin', null, [
                'label' => 'Fin'
            ])
        ;
    }
}
