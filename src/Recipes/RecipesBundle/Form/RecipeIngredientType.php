<?php

namespace Recipes\RecipesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecipeIngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('recipe')
            ->add('ingredient')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Recipes\RecipesBundle\Entity\RecipeIngredient'
        ));
    }

    public function getName()
    {
        return 'recipes_recipesbundle_recipeingredienttype';
    }
}
