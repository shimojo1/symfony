<?php

namespace App\Controller;

use App\Entity\Form;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FormController extends AbstractController
{
    private $validator;

    public function __construct(ValidatorInterface $validator) {
        $this->validator = $validator;
    }

    /**
     * @Route("/form", name="form")
     * @Route("/form/index", name="form_index")
     */
    public function index(SessionInterface $session): Response
    {
        /*
         * モデルからフォームで利用する
         * データ項目が格納されたハッシュ配列を取得
         */
        //セッションから情報を取得
        $form = $session->get('form') ?: new Form();

        return $this->render('form/index.html.twig', [
            'form' => $form,
            'errors' => [],
        ]);
    }

    /**
     * @Route("/form/confirm", name="form_confirm")
     */
    public function confirm(Request $request, SessionInterface $session): Response
    {
        //POSTリクエストでなければ404へリダイレクト
        $params = $request->request->all();
        if (
            !isset($params['name']) ||
            !isset($params['age']) ||
            !isset($params['prefecture']) ||
            !isset($params['address1']) ||
            !isset($params['address2']) ||
            !isset($params['comment'])
        ) {
            return $this->redirect('/form');
        }
        $form = new Form();
        $form
            ->setName($params['name'])
            ->setAge($params['age'])
            ->setPrefecture($params['prefecture'])
            ->setAddress1($params['address1'])
            ->setAddress2($params['address2'])
            ->setComment($params['comment']);

        $errors = $this->validator->validate($form);

        //エラーがない場合、確認画面を表示
        if (count($errors) === 0) {
            //入力されたデータをセッションに格納
            $session->set('form', $form);
            //Viewテンプレートに渡すデータ配列作成
            return $this->render('form/confirm.html.twig', [
                'form' => $params,
                'errors' => $errors,
                'base_url' => '/',
            ]);
        }
        //Viewテンプレートに渡すデータ配列作成エラー情報が渡される
        return $this->render('form/index.html.twig', [
            'form' => $form,
            'errors' => $errors,
        ]);
    }

    /**
     * @Route("/form/complete", name="form_complete")
     */
    public function complete(Request $request, SessionInterface $session): Response
    {
        //POSTリクエストでなければ404へリダイレクト
        if (!$request->isMethod('POST')) {
            return $this->redirectToRoute('form');
        }

        // セッションからフォームのデータを取得
        $form= $session->get('form');

        // Doctrineのエンティティマネージャーを取得
        $entityManager = $this->getDoctrine()->getManager();

        $form->updateTimestamps();

        // エンティティを作成して設定
        // エンティティを永続化（データベースに挿入）
        $entityManager->persist($form);
        $entityManager->flush();

        $session->remove('form');

        return $this->render('form/complete.html.twig', [
            'form' => $form,
        ]);
    }
}
