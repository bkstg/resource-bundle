<?php

namespace Bkstg\ResourceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Bkstg\CoreBundle\Form\DataTransformer\UserToNumberTransformer;

class ResourceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityManager = $options['em'];
        $userTransformer = new UserToNumberTransformer($entityManager);
        $builder
            ->add('title', 'text', array('label' => 'Resource title'))
            ->add('file')
            ->add(
                $builder->create('user', 'hidden')
                    ->addModelTransformer($userTransformer)
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Bkstg\ResourceBundle\Entity\Resource'
            ))
            ->setRequired(array('em'))
            ->setAllowedTypes('em', 'Doctrine\Common\Persistence\ObjectManager');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bkstg_resourcebundle_resource';
    }
}
