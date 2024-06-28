<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Length;

class FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => '名前',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => '名前を入力してください',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => '名前は少なくとも{{ limit }}文字以上で入力してください',
                        'maxMessage' => '名前は{{ limit }}文字以内で入力してください',
                    ]),
                ],
            ])
            ->add('age', IntegerType::class, [
                'label' => '年齢',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => '年齢を入力してください',
                    ]),
                    new PositiveOrZero([
                        'message' => '年齢は0以上の整数を入力してください',
                    ]),
                ],
            ])
            ->add('prefecture', TextType::class, [
                'label' => '都道府県',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => '都道府県を入力してください',
                    ]),
                ],
            ])
            ->add('address1', TextType::class, [
                'label' => '市区町村',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => '市区町村を入力してください',
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => '市区町村は{{ limit }}文字以内で入力してください',
                    ]),
                ],
            ])
            ->add('address2', TextType::class, [
                'label' => '番地',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => '番地を入力してください',
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => '番地は{{ limit }}文字以内で入力してください',
                    ]),
                ],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'コメント',
                'required' => false,
                'attr' => ['rows' => 4],
                'constraints' => [
                    new Length([
                        'max' => 1000,
                        'maxMessage' => 'コメントは{{ limit }}文字以内で入力してください',
                    ]),
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
