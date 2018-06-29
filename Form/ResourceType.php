<?php

namespace Bkstg\ResourceBundle\Form;

use Bkstg\ResourceBundle\BkstgResourceBundle;
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
     *
     * @param  FormBuilderInterface $builder The form builder.
     * @param  array                $options The form options.
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('media', MediaType::class, [
                'label' => 'resource.form.file',
                'provider' => 'sonata.media.provider.file',
                'context'  => 'default',
                'translation_domain' => BkstgResourceBundle::TRANSLATION_DOMAIN,
            ])
            ->add('name', TextType::class, [
                'label' => 'resource.form.name',
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'resource.form.description',
                'config' => ['toolbar' => 'basic'],
                'required' => false,
            ])
            ->add('active', ChoiceType::class, [
                'label' => 'resource.form.active',
                'choices' => [
                    'resource.form.active_choices.active' => true,
                    'resource.form.active_choices.closed' => false,
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
     *
     * @param  OptionsResolver $resolver The option resolver.
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'BkstgResourceBundle',
            'data_class' => Resource::class,
        ]);
    }
}
