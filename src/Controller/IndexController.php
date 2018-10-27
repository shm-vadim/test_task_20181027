<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\TransactionRepository;
use App\Service\TradeMaster;
use App\Entity\Transaction;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(TransactionRepository $transactionRepository, TradeMaster $tradeMaster) : Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            return $this->render('index/welcome.html.twig');
        }

        $companies = array_reduce($transactionRepository->createPortfolioByCurrentUser(), function (array $companies, $company) use ($tradeMaster) : array {
            $ticker = $company['ticker'];
            $sharesCount = $company['sharesCount'];
            $ago = function (string $interval) : \DateTimeInterface {
                return (new \DateTime())->sub(new \DateInterval($interval));
            };

            $companies[] = [
                'name' => $tradeMaster->getCompanyNameByTicker($ticker),
                'ticker' => $ticker,
                'sharesCount' => $sharesCount,
                'sharesCost' => $tradeMaster->getQuotationByTicker($ticker) * $sharesCount,
                'sharesCost1y' => $tradeMaster->getQuotationByTicker($ticker, $ago('P1Y')) * $sharesCount,
                'sharesCost2y' => $tradeMaster->getQuotationByTicker($ticker, $ago('P2Y')) * $sharesCount,
            ];

            return $companies;
        }, []);

        $totalPortfolio = array_reduce($companies, function (array $totalPortfolio, array $company) : array {
            foreach (['sharesCount', 'sharesCost', 'sharesCost1y', 'sharesCost2y'] as $indicator) {
                if (!isset($totalPortfolio[$indicator])) {
                    $totalPortfolio[$indicator] = 0;
                }

                $totalPortfolio[$indicator] += $company[$indicator];
            }

            return $totalPortfolio;
        }, []);

        return $this->render('index/index.html.twig', [
            'companies' => $companies,
            'totalPortfolio' => $totalPortfolio,
        ]);
    }
}
