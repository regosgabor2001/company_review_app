<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use App\Repository\CompanyRepository;
use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Main controller handling actions for viewing, creating, and analyzing company reviews.
 */
final class ReviewController extends AbstractController
{
    /**
     * Homepage / Index action.
     * Displays a paginated list of reviews with optional search filter and rating sorting.
     */
    #[Route('/', name: 'app_review_index', methods: ['GET'])]
    public function index(Request $request, ReviewRepository $reviewRepository): Response
    {
        // Read pagination and filter parameters from the query string
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 10;
        $search = $request->query->get('search');
        
        // Normalize sort direction and enforce a valid value (default to 'desc')
        $sort = strtolower($request->query->get('sort', 'desc'));
        if (!in_array($sort, ['asc', 'desc'], true)) {
            $sort = 'desc';
        }

        // Query paginated reviews with optional company search and rating sort
        $reviews = $reviewRepository->findPaginatedWithSearchAndSort($page, $limit, $search, $sort);
        $total = $reviewRepository->countWithSearch($search);

        return $this->render('review/index.html.twig', [
            'reviews' => $reviews,
            'page' => $page,
            'totalPages' => ceil($total / $limit),
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    /**
     * Submission action for creating a new review.
     * Handles form rendering, validation, database persistence, and user feedback.
     */
    #[Route('/new', name: 'app_review_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        // Process form submission if valid
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();

            // Set mandatory flash message required by the specification (Section 2.1)
            $this->addFlash('success', 'Köszönjük a véleményed!');

            // Redirect using HTTP 303 See Other as recommended for form post-redirect patterns
            return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('review/new.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    /**
     * Aggregated Company Statistics action (Mandatory - Section 2.4).
     * Lists all companies, their review counts, and average ratings sorted in descending order.
     */
    #[Route('/companies', name: 'app_company_statistics', methods: ['GET'])]
    public function companies(CompanyRepository $companyRepository): Response
    {
        // Fetch aggregates using optimized database queries
        $companyStats = $companyRepository->findAllWithReviewStats();

        return $this->render('review/companies.html.twig', [
            'companies' => $companyStats,
        ]);
    }

    /**
     * Detailed view action for an individual review record.
     * Uses Symfony's ParamConverter to automatically fetch the Review entity by ID.
     */
    #[Route('/{id}', name: 'app_review_show', methods: ['GET'])]
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    /*#[Route('/{id}/edit', name: 'app_review_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_review_delete', methods: ['POST'])]
    public function delete(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
    }*/
}
