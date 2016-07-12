<?php

namespace ActuBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActuType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('titre', 'text')
                ->add('actuLgTa', 'ckeditor', array(
                    'label' => 'contenu pour ecran HD', 'required' => true))
                ->add('actuMdTa', 'ckeditor', array(
                    'label' => 'contenu pour ecran medium',
                    'config_name' => 'configlite', 'required' => true))
                ->add('actuSmTa', 'ckeditor', array(
                    'label' => 'contenu pour tablette',
                    'config_name' => 'configlite'))
                ->add('actuXsTa', 'ckeditor', array(
                    'label' => 'contenu pour smartphone',
                    'config_name' => 'configlite'))
                ->add('auteur', 'text')
                ->add('publier', 'checkbox', array('required' => false))
                ->add('envoie', 'submit')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ActuBundle\Entity\Actu'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'actubundle_actu';
    }

}
