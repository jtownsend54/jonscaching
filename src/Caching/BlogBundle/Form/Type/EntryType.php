<?php

namespace Caching\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class EntryType extends AbstractType
{
    public function buildForm(Formbuilder $builder, array $options)
    {
        $builder->add('title');
        $builder->add('route_area');
        $builder->add('attachment', 'file');
        $builder->add('entry', 'textarea');
    }
    
    public function getName()
    {
        return 'entry';
    }
}