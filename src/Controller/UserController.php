<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Security\Voter\BookmarksVoter;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
			$postRepository->userPosts($user->getId()),
			$request->query->getInt('page', 1),
			PostController::LIMIT_PER_PAGE
		);

		return $this->render('user/posts.html.twig', [
			'pagination' => $pagination,
			'user'       => $user
		]);
	}

	/**
	 * @Route("/{nickname}/bookmarks", name="user_bookmarks", methods={"GET"})
	 * @param User               $user
	 * @param Request            $request
	 * @param PostRepository     $postRepository
	 * @param PaginatorInterface $paginator
	 *
	 * @return Response
	 */
	public function bookmarks(User $user, Request $request, PostRepository $postRepository, PaginatorInterface $paginator): Response
	{
		$this->denyAccessUnlessGranted(BookmarksVoter::SHOW, $user, 'Authors can only see bookmarks!');

		$pagination = $paginator->paginate(
			$postRepository->userBookmarks($user->getId()),
			$request->query->getInt('page', 1),
			PostController::LIMIT_PER_PAGE
		);

		return $this->render('user/bookmarks.html.twig', [
			'pagination' => $pagination,
			'user'       => $user,
		]);
	}

	/**
	 * @Route("/add-to-bookmark/{slug}", name="user_add_to_bookmark", methods={"GET"})
	 * @IsGranted("ROLE_USER")
	 * @param Post $post
	 *
	 * @return RedirectResponse
	 */
	public function addToBookmark(Post $post): RedirectResponse
	{
		$user = $this->getUser();

		if ($user === null)
		{
			return $this->redirectToRoute('blog_index');
		}

		$user->addBookmark($post);
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($user);
		$entityManager->flush();

		$this->addFlash('success', sprintf('Post <b>%s</b> added to bookmark!', $post->getTitle()));

		return $this->redirectToRoute('blog_index');

	}

	/**
	 * @Route("/remove-from-bookrmaks/{slug}", name="user_remove_from_bookmarks", methods={"GET"})
	 * @param Post $post
	 *
	 * @return RedirectResponse
	 */
	public function removeFromBookmarks(Post $post): RedirectResponse
	{
		$user = $this->getUser();

		if ($user === null)
		{
			return $this->redirectToRoute('blog_index');
		}

		$user->removeBookmark($post);
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($user);
		$entityManager->flush();

		$this->addFlash('success', sprintf('Post <b>%s</b> removed from bookmarks!', $post->getTitle()));

		return $this->redirectToRoute('blog_index');
	}

}
