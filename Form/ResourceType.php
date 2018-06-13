<?php

namespace Bkstg\ResourceBundle\Form;

use Bkstg\ResourceBundle\Entity\Resource;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', MediaType::class, [
                'label' => 'resource.form.image',
                'provider' => 'sonata.media.provider.file',
                'context'  => 'default',
            ])
            ->add('name', TextType::class, [
                'label' => 'resource.form.name',
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'resource.form.body',
                'config' => ['toolbar' => 'basic'],
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'resource.form.status',
                'choices' => [
                    'Active' => true,
                    'Closed' => false,
                ],
            ])
            ->add('pinned', CheckboxType::class, [
                'label' => 'resource.form.pinned',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'BkstgResourceBundle',
            'data_class' => Resource::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bkstg_resourcebundle_resource';
    }
}
