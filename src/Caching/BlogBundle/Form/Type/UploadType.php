<?php

namespace Caching\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UploadType extends AbstractType
{
    public function buildForm(Formbuilder $builder, array $options)
    {
        $builder->add('route_name');
        $builder->add('route_date', 'text', array('attr' => array('class' => 'datepicker')));
        $builder->add('attachment', 'file');
    }
    
    public function getName()
    {
        return 'upload';
    }
}
