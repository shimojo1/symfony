<?php

namespace App\Controller;

use App\Entity\Form;
use App\Form\FormType;
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
    public function index(Request $request, SessionInterface $session): Response
    {
        // セッションから情報を取得
        $form = $session->get('form') ?: new Form();
        // フォームの作成
        $form = $this->createForm(FormType::class, $form);

        // リクエストを検証してバリデーションを行う
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // フォームデータをセッションに保存
            $session->set('form', $form->getData());
            // フラグを設定して確認画面へのアクセスを許可
            $session->set('confirm_access', true);

            // フォームが送信され、バリデーションに成功した場合確認画面へリダイレクトする
            return $this->redirectToRoute('form_confirm', ['form' => $form->getData()]);
        }

        return $this->render('form/index.html.twig', [
            'form' => $form->createView(),
            'errors' => [],
        ]);
    }

    /**
     * @Route("/form/confirm", name="form_confirm")
     */
    public function confirm(Request $request, SessionInterface $session): Response
    {
        // フラグをチェックしてアクセスを制限
        if (!$session->get('confirm_access', false)) {
            return $this->redirectToRoute('form');
        }
        // フラグをリセット
        $session->remove('confirm_access');

        // セッションからフォームデータを取得
        $form = $session->get('form');

        // フォームデータがない場合、フォーム入力画面にリダイレクト
        if (!$form) {
            return $this->redirectToRoute('form');
        }

        // 確認画面を表示
        return $this->render('form/confirm.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/form/complete", name="form_complete")
     */
    public function complete(Request $request, SessionInterface $session): Response
    {
        // セッションからフォームデータを取得
        $form = $session->get('form');

        // POSTリクエストでなければ404へリダイレクト
        // フォームデータがない場合、フォーム入力画面にリダイレクト
        if (!$request->isMethod('POST') || !$form) {
            return $this->redirectToRoute('form');
        }

        // Doctrineのエンティティマネージャーを取得
        $entityManager = $this->getDoctrine()->getManager();

        // created_at,updated_atに現在時刻を入れる_
        $form->updateTimestamps();

        // エンティティを作成して設定
        // エンティティを永続化（データベースに挿入）
        $entityManager->persist($form);
        $entityManager->flush();

        // セッション消去
        $session->remove('form');

        return $this->render('form/complete.html.twig', [
            'form' => $form,
        ]);
    }
}
