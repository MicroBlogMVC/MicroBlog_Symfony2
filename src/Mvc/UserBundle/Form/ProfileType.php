<?php 

namespace Mvc\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('bio', 'textarea', array(
                    'attr' => array ('rows' => 8 )
                ))
                ->add('avatar', 'file', array(
                        'property_path' => 'file'
                    )
                )
        ;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mvc\UserBundle\Entity\User'
        ));
    }


    public function getName()
    {
        return 'mvc_userbundle_profiletype';
    }

}