<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RepeatedPasswordType
 *
 * @uses AbstractType
 * @package
 * @version $id$
 * @copyright
 * @author Nobuhiko Kimoto <info@nob-log.info>
 * @license
 */
class RepeatedPasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function __construct($config = array('password_min_len' => 8, 'password_max_len' => '32'))
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'type' => 'text', // type password だと入力欄を空にされてしまうので、widgetで対応
            'required' => true,
            'error_bubbling' => false,
            'invalid_message' => 'form.member.password.invalid',
            'options' => array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'min' => $this->config['password_min_len'],
                        'max' => $this->config['password_max_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                        'message' => 'form.type.graph.invalid',
                    )),
                ),
            ),
            'first_options' => array(
                'attr' => array(
                    'placeholder' => '半角英数字記号'.$this->config['password_min_len'].'～'.$this->config['password_max_len'].'文字',
                ),
            ),
            'second_options' => array(
                'attr' => array(
                    'placeholder' => 'form.member.repeated.confirm',
                ),
            ),
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
        return 'repeated_password';
    }
}
