<?php 

namespace Mvc\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MessageType extends AbstractType 
{
    
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('message', 'textarea', array(
                'attr' => array ('rows' => 6 )
            ));
    }
    

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mvc\BlogBundle\Entity\Message'
        ));
    }

    

    public function getName()
    {
        return 'mvc_blogbundle_messagetype';
    }

}