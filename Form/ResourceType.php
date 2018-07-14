<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgResourceBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\ResourceBundle\Form;

use Bkstg\ResourceBundle\BkstgResourceBundle;
use Bkstg\ResourceBundle\Entity\Resource;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
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
     * @param FormBuilderInterface $builder The form builder.
     * @param array                $options The form options.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $resource = $options['data'];
        $builder
            ->add('media', MediaType::class, [
                'label' => 'resource.form.file',
                'provider' => 'sonata.media.provider.file',
                'context' => 'default',
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
                    // Show "unpublished" instead of active.
                    'choice_loader' => new CallbackChoiceLoader(function () use ($resource) {
                        yield 'resource.form.status_choices.active' => true;
                        if (!$resource->isPublished()) {
                            yield 'resource.form.status_choices.unpublished' => false;
                        } else {
                            yield 'resource.form.status_choices.archived' => false;
                        }
                    }),
                    'label' => 'resource.form.status',
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
     * @param OptionsResolver $resolver The option resolver.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'BkstgResourceBundle',
            'data_class' => Resource::class,
        ]);
    }
}
