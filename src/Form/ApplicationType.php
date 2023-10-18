<?php
namespace App\Form;

class ApplicationType extends AbstractType
{
    private function getConfiguration(string $label, string $placeholder, array $options=[]):array
    {
        return array_merge_recursive([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ],
        ], $options
        );
        
    }


}



?>