<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Security\Voter\BookmarksVoter;
use App\Services\PostPagination;
use App\Services\PostPaginationSortQuery;
use App\Services\UrlRemember;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends AbstractController
{
	/**
	 * @Route("/{nickname}/posts", name="user_posts", methods={"GET"})
	 * @param Request                 $request
	 * @param UrlRemember             $urlRemember
	 * @param User                    $user	 *
	 * @param PostPagination          $pagination
	 * @param PostPaginationSortQuery $paginationSortQuery
	 *
	 * @return Response
	 */
	public function posts(Request $request, UrlRemember $urlRemember, User $user, PostPagination $pagination, PostPaginationSortQuery $paginationSortQuery): Response
	{
		$urlRemember->remember();

		$paginator = $pagination->pagination($paginationSortQuery->user($user->getId()));

		if ($request->isXmlHttpRequest())
		{
			return $this->render('post/_items.html.twig', ['pagination' => $paginator]);
		}

		return $this->render('user/posts.html.twig', [
			'pagination' => $paginator,
			'user'       => $user
		]);
	}

	/**
	 * @Route("/{nickname}/bookmarks", name="user_bookmarks", methods={"GET"})
	 * @param UrlRemember             $urlRemember
	 * @param User                    $user
	 * @param Request                 $request
	 * @param PostPagination          $pagination
	 * @param PostPaginationSortQuery $paginationSortQuery
	 *
	 * @return Response
	 */
	public function bookmarks(UrlRemember $urlRemember, User $user, Request $request, PostPagination $pagination, PostPaginationSortQuery $paginationSortQuery): Response
	{
		$this->denyAccessUnlessGranted(BookmarksVoter::SHOW, $user, 'Authors can only see bookmarks!');

		$urlRemember->remember();

		$paginator = $pagination->pagination($paginationSortQuery->userBookmarks($user->getId()));

		if ($request->isXmlHttpRequest())
		{
			return $this->render('post/_items.html.twig', ['pagination' => $paginator]);
		}

		return $this->render('user/bookmarks.html.twig', [
			'pagination' => $paginator,
			'user'       => $user,
		]);
	}

	/**
	 * @Route("/add-to-bookmarks/{slug}", name="user_add_to_bookmarks", methods={"GET"})
	 * @IsGranted("ROLE_USER")
	 * @param UrlRemember         $urlRemember
	 * @param Post                $post
	 * @param TranslatorInterface $translator
	 *
	 * @return RedirectResponse
	 */
	public function addToBookmarks(UrlRemember $urlRemember, Post $post, TranslatorInterface $translator): RedirectResponse
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

		$this->addFlash('success', sprintf($translator->trans('post.bookmarks.messages.add'), $post->getTitle()));

		return $this->redirect($urlRemember->previous());
	}

	/**
	 * @Route("/remove-from-bookrmaks/{slug}", name="user_remove_from_bookmarks", methods={"GET"})
	 * @param UrlRemember         $urlRemember
	 * @param Post                $post	 *
	 * @param TranslatorInterface $translator
	 *
	 * @return RedirectResponse
	 */
	public function removeFromBookmarks(UrlRemember $urlRemember, Post $post, TranslatorInterface $translator): RedirectResponse
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

		$this->addFlash('success', sprintf($translator->trans('post.bookmarks.messages.delete'), $post->getTitle()));

		return $this->redirect($urlRemember->previous());
	}

	/**
	 * @Route("/{nickname}/posts/search", name="user_posts_search", methods={"GET"})
	 * @param Request                 $request
	 * @param User                    $user
	 * @param UrlRemember             $urlRemember
	 * @param PostPagination          $pagination
	 * @param PostRepository          $postRepository
	 *
	 * @param PostPaginationSortQuery $paginationSortQuery
	 *
	 * @return Response
	 */
	public function search(Request $request, User $user, UrlRemember $urlRemember, PostPagination $pagination, PostRepository $postRepository, PostPaginationSortQuery $paginationSortQuery): Response
	{
		$urlRemember->remember();

		if (!empty($request->get('q')))
		{
			$query = $postRepository->searchWithUser($request->get('q'), $user->getId());
		}

		$paginator = $pagination->pagination($query ?? $paginationSortQuery->user($user->getId()));

		if ($request->isXmlHttpRequest())
		{
			return $this->render('post/_items.html.twig', ['pagination' => $paginator]);
		}

		return $this->render('user/posts.html.twig', [
			'pagination' => $paginator,
			'user'       => $user
		]);
	}

	/**
	 * @Route("/{nickname}/bookrmaks/search", name="user_bookmarks_posts_search", methods={"GET"})
	 * @param Request                 $request
	 * @param User                    $user
	 * @param UrlRemember             $urlRemember
	 * @param PostPagination          $pagination
	 * @param PostRepository          $postRepository
	 *
	 * @param PostPaginationSortQuery $paginationSortQuery
	 *
	 * @return Response
	 */
	public function searchBookmarks(Request $request, User $user, UrlRemember $urlRemember, PostPagination $pagination, PostRepository $postRepository, PostPaginationSortQuery $paginationSortQuery): Response
	{
		$this->denyAccessUnlessGranted(BookmarksVoter::SHOW, $user, 'Authors can only see bookmarks!');

		$urlRemember->remember();

		if (!empty($request->get('q')))
		{
			$query = $postRepository->searchInBookmarks($request->get('q'), $user->getId());
		}

		$paginator = $pagination->pagination($query ?? $paginationSortQuery->userBookmarks($user->getId()));

		if ($request->isXmlHttpRequest())
		{
			return $this->render('post/_items.html.twig', ['pagination' => $paginator]);
		}

		return $this->render('user/bookmarks.html.twig', [
			'pagination' => $paginator,
			'user'       => $user
		]);
	}

}
