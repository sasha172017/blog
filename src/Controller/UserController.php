<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends AbstractController
{
	/**
	 * @Route("/{nickname}/posts", name="user_posts", methods={"GET"})
	 * @param User               $user
	 *
	 * @param Request            $request
	 * @param PostRepository     $postRepository
	 * @param PaginatorInterface $paginator
	 *
	 * @return Response
	 */
	public function posts(User $user, Request $request, PostRepository $postRepository, PaginatorInterface $paginator): Response
	{
		$pagination = $paginator->paginate(
			$postRepository->paginationWithUser($user->getId()),
			$request->query->getInt('page', 1),
			PostController::LIMIT_PER_PAGE
		);

		return $this->render('post/index.html.twig', [
			'pagination' => $pagination,
			'user'   => $user
		]);
	}
}
