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
    public function new(Request $request, UserLoader $userLoader, TradeMaster $tradeMaster, TransactionRepository $transactionRepository) : Response
    {
        $this->denyAccessUnlessGranted('CREATE_TRANSACTIONS');

        $currentUser = $userLoader->getUser();
        $transaction = (new Transaction())
            ->setUser($currentUser);
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);
        $leftSharesCount = !$transaction->isBuy() ? $transactionRepository->getTotalSharesCountByCurrentUserAndTicker($transaction->getCompanyTicker()) - $transaction->getSharesCount() : 0;

        if ($form->isSubmitted() && $form->isValid() && $leftSharesCount >= 0) {
            $money = $tradeMaster->getQuotationByTicker($transaction->getCompanyTicker()) * $transaction->getSharesCount();
            $transaction->setMoney(
                !$transaction->isBuy() ? $money : -1 * $money
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
        $this->denyAccessUnlessGranted('SHOW', $transaction);

        return $this->render('transaction/show.html.twig', ['transaction' => $transaction]);
    }

    /**
     * @Route("/{id}/edit", name="transaction_edit", methods="GET|POST")
     */
    public function edit(Request $request, Transaction $transaction) : Response
    {
        $this->denyAccessUnlessGranted('EDIT', $transaction);

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
        $this->denyAccessUnlessGranted('DELETE', $transaction);

        if ($this->isCsrfTokenValid('delete' . $transaction->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction);
            $em->flush();
        }

        return $this->redirectToRoute('transaction_index');
    }
}
