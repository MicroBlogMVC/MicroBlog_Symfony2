<?php 

namespace Mvc\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType 
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text')
                ->add('password', 'repeated', array(
                      'type' => 'password',
                      'invalid_message' => 'The password fields must match.',
                      'required' => true,
                      'first_options'  => array('label' => 'Password'),
                      'second_options' => array('label' => 'Confirm Password'),
                    ));
    }



    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mvc\UserBundle\Entity\User'
        ));
    }



    public function getName()
    {
        return 'mvc_userbundle_usertype';
    }


}