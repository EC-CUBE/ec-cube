<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RepeatedEmailType
 *
 * @uses AbstractType
 * @package
 * @version $id$
 * @copyright
 * @author Nobuhiko Kimoto <info@nob-log.info>
 * @license
 */
class RepeatedEmailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'type' => 'email',
            'required' => true,
            'invalid_message' => 'form.member.email.invalid',
            'options' => array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(array('strict' => true)),
                    new Assert\Regex(array(
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                        'message' => 'form.type.graph.invalid',
                    )),
                ),
            ),
            'second_options' => array(
                'attr' => array(
                    'placeholder' => 'form.member.repeated.confirm',
                ),
            ),
            'error_bubbling' => false,
            'trim' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'repeated';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'repeated_email';
    }
}
