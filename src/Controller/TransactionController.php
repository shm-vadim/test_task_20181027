<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserLoader;
use App\Service\TradeMaster;

/**
 * @Route("/transaction")
 */
class TransactionController extends AbstractController
{
    /**
     * @Route("/", name="transaction_index", methods="GET")
     */
    public function index(TransactionRepository $transactionRepository) : Response
    {
        return $this->render('transaction/index.html.twig', ['transactions' => $transactionRepository->findAll()]);
    }

    /**
     * @Route("/new", name="transaction_new", methods="GET|POST")
     */
    public function new(Request $request, UserLoader $userLoader, TradeMaster $tradeMaster) : Response
    {
        $this->denyAccessUnlessGranted('CREATE_TRANSACTION');

        $currentUser = $userLoader->getUser();
        $transaction = (new Transaction())
            ->setUser($currentUser);
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->setMoney(
                $tradeMaster->getQuotationByCompanyTicker($transaction->getCompanyTicker()) * $transaction->getSharesCount()
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            return $this->redirectToRoute('transaction_index');
        }

        return $this->render('transaction/new.html.twig', [
            'transaction' => $transaction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transaction_show", methods="GET")
     */
    public function show(Transaction $transaction) : Response
    {
        return $this->render('transaction/show.html.twig', ['transaction' => $transaction]);
    }

    /**
     * @Route("/{id}/edit", name="transaction_edit", methods="GET|POST")
     */
    public function edit(Request $request, Transaction $transaction) : Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transaction_edit', ['id' => $transaction->getId()]);
        }

        return $this->render('transaction/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transaction_delete", methods="DELETE")
     */
    public function delete(Request $request, Transaction $transaction) : Response
    {
        if ($this->isCsrfTokenValid('delete' . $transaction->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction);
            $em->flush();
        }

        return $this->redirectToRoute('transaction_index');
    }
}
