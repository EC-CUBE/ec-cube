<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Form\Extension;

use Doctrine\Persistence\ObjectManager;
use Eccube\Util\IdUtil;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceValue;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * 整数型の ID を持つ EntityType で、範囲外の値を DB へ問い合わせる前に入力エラーにする.
 *
 * Symfony の ORMQueryBuilderLoader は数字以外の値を取り除くが、桁数は見ないため
 * PostgreSQL では out of range (22003) で例外になる.
 */
class EntityIdTypeExtension extends AbstractTypeExtension
{
    /**
     * @param array<string, mixed> $options
     */
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // choice_value を独自に指定している場合は、送信値が ID とは限らないため対象外
        if (!$options['choice_value'] instanceof ChoiceValue) {
            return;
        }

        $max = $this->getMaxId($options['em'] ?? null, $options['class'] ?? null);
        if (null === $max) {
            return;
        }

        // ChoiceType は multiple / expanded の場合 PRE_SUBMIT で選択肢を DB から引くため、それより先に検証する.
        // PRE_SUBMIT で投げた TransformationFailedException は、Form::submit() で入力エラーとして扱われる
        $builder->addEventListener(FormEvents::PRE_SUBMIT, static function (FormEvent $event) use ($max): void {
            $data = $event->getData();
            foreach (is_array($data) ? $data : [$data] as $value) {
                if (null === $value || '' === $value) {
                    continue;
                }
                if (null === IdUtil::toId($value, $max)) {
                    throw new TransformationFailedException(sprintf('The choice "%s" is not a valid identifier.', is_scalar($value) ? $value : get_debug_type($value)));
                }
            }
        }, 1024);
    }

    private function getMaxId(mixed $em, mixed $class): ?int
    {
        if (!$em instanceof ObjectManager || !is_string($class) || !class_exists($class)) {
            return null;
        }

        $metadata = $em->getClassMetadata($class);
        $identifiers = $metadata->getIdentifierFieldNames();
        if (1 !== count($identifiers)) {
            return null;
        }

        return IdUtil::maxForType($metadata->getTypeOfField($identifiers[0]));
    }

    #[\Override]
    public static function getExtendedTypes(): iterable
    {
        return [EntityType::class];
    }
}
