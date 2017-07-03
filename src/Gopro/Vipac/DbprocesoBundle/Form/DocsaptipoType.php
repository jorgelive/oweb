<?php

namespace Gopro\Vipac\DbprocesoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocsaptipoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('tiposunat',null, array('label' => 'Tipo Sunat'))
            ->add('tiposap',null, array('label' => 'Tipo Sap'))
            ->add('exoneradoigv',null, array('label' => 'Exonerado IGV'))
            ->add('iscporcentaje',null, array('label' => 'Porcentaje ISC', 'required' => false))
            ->add('codigoretencion',null, array('label' => 'Código de Retención', 'required' => false))
            ->add('cuenta',null, array('label' => 'Cuenta'))
            ->add('tiposervicio',null, array('label' => 'Tipo de servicio'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gopro\Vipac\DbprocesoBundle\Entity\Docsaptipo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_vipac_dbprocesobundle_docsaptipo';
    }
}