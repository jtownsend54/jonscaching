<?php

namespace Caching\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class EntryType extends AbstractType
{
    public function buildForm(Formbuilder $builder, array $options)
    {
        $builder->add('title');
        $builder->add('entry');
        $builder->add('routes', 'entity', array(
            'class'     => 'CachingBlogBundle:Route',
            'multiple'  => true,
            'property'  => 'name',
        ));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class'        => 'Caching\BlogBundle\Entity\Entry',
        );
    }
    
    public function getName()
    {
        return 'entry';
    }
}