<?php

namespace Gopro\CotizacionBundle\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Show\ShowMapper;


use Gopro\ServicioBundle\Entity\Componente;
use Gopro\ServicioBundle\Repository\TarifaRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class CottarifaAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('cantidad')
            ->add('cotcomponente',  null, [
                'label' => 'Componente'
            ])
            ->add('tarifa')
            ->add('moneda')
            ->add('monto')
            ->add('tipotarifa',  null, [
                'label' => 'Tipo'
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('cantidad')
            ->add('cotcomponente',  null, [
                'label' => 'Componente'
            ])
            ->add('tarifa')
            ->add('moneda')
            ->add('monto')
            ->add('tipotarifa',  null, [
                'label' => 'Tipo'
            ])
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
        if ($this->getRoot()->getClass() != 'Gopro\CotizacionBundle\Entity\File'
            && $this->getRoot()->getClass() != 'Gopro\CotizacionBundle\Entity\Cotizacion'
            && $this->getRoot()->getClass() != 'Gopro\CotizacionBundle\Entity\Cotservicio'
            && $this->getRoot()->getClass() != 'Gopro\CotizacionBundle\Entity\Cotcomponente'
        ){
            $formMapper->add('cotcomponente',  null, [
                'label' => 'Componente'
            ]);
        }

        $formMapper
            ->add('tarifa', ModelAutocompleteType::class, [
                'property' => 'nombre',
                'template' => 'GoproCotizacionBundle:Form:ajax_dropdown_type.html.twig',
                'route' => ['name' => 'gopro_servicio_tarifa_porcomponentedropdown', 'parameters' => []],
                'placeholder' => '',
                'context' => '/\[cottarifas\]\[\d\]\[tarifa\]$/g, "[componente]"',
                'minimum_input_length' => 0,
                'dropdown_auto_width' => false
            ])
            ->add('cantidad')
            ->add('moneda')
            ->add('monto')
            ->add('tipotarifa',  null, [
                'label' => 'Tipo'
            ])
        ;

        $cantidadModifier = function (FormInterface $form, $clases) {

            $form->add(
                'cantidad',
                null,
                [
                    'label' => 'Cantidad',
                    'attr' => ['class' => $clases]
                ]
            );
        };

        $formBuilder = $formMapper->getFormBuilder();
        $formBuilder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($cantidadModifier) {

                if($event->getData()
                    && $event->getData()->getTarifa()
                    && $event->getData()->getTarifa()->getProrrateado() === true
                ){
                    if($event->getData()->getTarifa()->getCapacidadmax() == 1){
                        $clases = 'prorrateado inputwarning';
                    }else{
                        $clases = 'prorrateado readonly';
                    }

                    //var_dump($event->getData()->getComponente()->getTipocomponente()->getDependeduracion());
                    $cantidadModifier($event->getForm(), $clases);
                }
            }
        );

    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('cotcomponente',  null, [
                'label' => 'Componente'
            ])
            ->add('tarifa')
            ->add('cantidad')
            ->add('moneda')
            ->add('monto')
            ->add('tipotarifa',  null, [
                'label' => 'Tipo'
            ])
        ;
    }
}
