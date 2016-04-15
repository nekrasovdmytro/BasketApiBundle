<?php

namespace Binary\Bundle\FruitBasketApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('weight', TextType::class)
            ->add('type', EntityType::class, [
                'class' => 'BasketApiBundle:Type',
                'choice_label' => function ($type) {
                    return $type->getName();
                },
                'choice_value' => function ($type) {
                    return $type->getName(); //name has unique index
                }
            ])
            ->add('basket', EntityType::class, [
                'class' => 'BasketApiBundle:Basket',
                'choice_label' => function ($basket) {
                    return $basket->getName();
                },
                'choice_value' => function ($basket) {
                    return $basket->getId();
                }
            ])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Binary\Bundle\FruitBasketApiBundle\Entity\Item',
            'csrf_protection'   => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'binary_bundle_fruitbasketapibundle_item';
    }
}
