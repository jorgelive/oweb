<?php

namespace Gopro\TransporteBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ServiciocontableAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('servicio.dependencia', null, [
                'label' => 'Cliente',
            ])
            ->add('servicio.nombre', null, [
                'label' => 'Servicio'
            ])
            ->add('servicio.fechahorainicio', 'doctrine_orm_callback', [
                'label' => 'Fecha de servicio',
                'callback' => function($queryBuilder, $alias, $field, $value) {
                    if (!$value['value'] || !($value['value'] instanceof \DateTime)) {
                        return;
                    }
                    $fechaMasUno = clone ($value['value']);
                    $fechaMasUno->add(new \DateInterval('P1D'));
                    $queryBuilder->andWhere("DATE($alias.fechahorainicio) >= :fechahora");
                    $queryBuilder->andWhere("DATE($alias.fechahorainicio) < :fechahoraMasUno");
                    $queryBuilder->setParameter('fechahora', $value['value']);
                    $queryBuilder->setParameter('fechahoraMasUno', $fechaMasUno);
                    return true;
                },
                'field_type'=>'sonata_type_date_picker',
                'field_options'=> [
                    'dp_use_current' => true,
                    'dp_show_today' => true,
                    'format'=> 'yyyy/MM/dd'
                ]
             ])
            ->add('estadocontable', null, [
                'label' => 'Estado'
            ])
            ->add('tiposercontable', null, [
                'label' => 'Tipo'
            ])
            ->add('descripcion', null, [
                'label' => 'Descripción'
            ])
            ->add('moneda')
            ->add('total')
            ->add('serie')
            ->add('documento')
            ->add('fechaemision', 'doctrine_orm_datetime', [
                'label' => 'Fecha emisión',
                'field_type'=>'sonata_type_date_picker',
                'field_options'=> [
                    'dp_use_current' => true,
                    'dp_show_today' => true,
                    'format'=> 'yyyy/MM/dd'
                ]
            ])
            ->add('url')
            ->add('original')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('servicio', null, [
                'route' => ['name' => 'show']
            ])
            ->add('servicio.dependencia', null, [
                'label' => 'Cliente',
            ])
            ->add('servicio.fechahorainicio', null, [
                'label' => 'Fecha servicio',
                'format' => 'Y/m/d H:i'

            ])
            ->add('estadocontable', null, [
                'label' => 'Estado',
                'route' => ['name' => 'show']
            ])
            ->add('tiposercontable', null, [
                'label' => 'Tipo',
                'route' => ['name' => 'show']
            ])
            ->add('moneda', null, [
                'route' => ['name' => 'show']
            ])
            ->add('total')
            ->add('serie')
            ->add('documento')
            ->add('fechaemision', null, [
                'label' => 'Fecha emisión'
            ])
            ->add('original')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'facturar' => [
                        'template' => 'GoproTransporteBundle:ServiciocontableAdmin:list__action_facturar.html.twig'
                    ]
                ]
            ])
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('servicio')
            ->add('estadocontable', null, [
                'label' => 'Estado'
            ])
            ->add('tiposercontable', null, [
                'label' => 'Tipo'
            ])
            ->add('descripcion', null, [
                'label' => 'Descripción'
            ])
            ->add('moneda')
            ->add('neto')
            ->add('impuesto')
            ->add('total')
            ->add('original')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('servicio.dependencia', null, [
                'label' => 'Cliente',
            ])
            ->add('servicio')
            ->add('servicio.id', null, [
                'label' => 'Identificador de servicio',
            ])
            ->add('servicio.fechahorainicio', null, [
                'label' => 'Fecha servicio',
                'format' => 'Y/m/d H:i'
            ])
            ->add('estadocontable', null, [
                'label' => 'Estado',
                'route' => ['name' => 'show']
            ])
            ->add('tiposercontable', null, [
                'label' => 'Tipo',
            ])
            ->add('descripcion', null, [
                'label' => 'Descripción'
            ])
            ->add('moneda', null, [
                'route' => ['name' => 'show']
            ])
            ->add('neto')
            ->add('impuesto')
            ->add('total')
            ->add('serie')
            ->add('documento')
            ->add('fechaemision', null, [
                'label' => 'Fecha emisión'
            ])
            ->add('url')
            ->add('original')
            ->add('sercontablemensajes', null, [
                'label' => 'Mensajes'
            ])
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('facturar', $this->getRouterIdParameter() . '/facturar');
    }

}
