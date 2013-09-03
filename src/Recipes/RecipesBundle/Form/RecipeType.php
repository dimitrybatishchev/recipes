<?php

namespace Recipes\RecipesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('category', 'entity', array('class'=>'Recipes\RecipesBundle\Entity\Category', 'property'=>'name',))
            ->add('cuisine', 'entity', array('class'=>'Recipes\RecipesBundle\Entity\Cuisine', 'property'=>'name',))
            ->add('file')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Recipes\RecipesBundle\Entity\Recipe'
        ));
    }

    public function getName()
    {
        return 'recipes_recipesbundle_recipetype';
    }
}
