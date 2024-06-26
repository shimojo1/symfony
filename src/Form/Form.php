<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => '名前',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 100]),
                ],
            ])
            ->add('age', IntegerType::class, [
                'label' => '年齢',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\PositiveOrZero(),
                ],
            ])
            ->add('prefecture', TextType::class, [
                'label' => '都道府県',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 50]),
                ],
            ])
            ->add('address1', TextType::class, [
                'label' => '市区町村',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 100]),
                ],
            ])
            ->add('address2', TextType::class, [
                'label' => '番地',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 100]),
                ],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'コメント',
                'required' => false,
                'attr' => ['rows' => 4],
                'constraints' => [
                    new Assert\Length(['max' => 1000]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // データクラスを指定する場合はここで指定するが、今回は使用していない
            // 'data_class' => 'App\Entity\YourEntity',
        ]);
    }
}
