<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Security\Voter\BookmarksVoter;
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
	 * @param UrlRemember        $urlRemember
	 * @param User               $user
	 *
	 * @param Request            $request
	 * @param PostRepository     $postRepository
	 * @param PaginatorInterface $paginator
	 *
	 * @param int                $postLimitPerPage
	 *
	 * @return Response
	 */
	public function posts(UrlRemember $urlRemember, User $user, Request $request, PostRepository $postRepository, PaginatorInterface $paginator, int $postLimitPerPage): Response
	{
		$urlRemember->remember();

		$pagination = $paginator->paginate(
			$postRepository->userPosts($user->getId()),
			$request->query->getInt('page', 1),
			$postLimitPerPage
		);

		if ($request->isXmlHttpRequest())
		{
			return $this->render('post/_items.html.twig', ['pagination' => $pagination]);
		}

		return $this->render('user/posts.html.twig', [
			'pagination' => $pagination,
			'user'       => $user
		]);
	}

	/**
	 * @Route("/{nickname}/bookmarks", name="user_bookmarks", methods={"GET"})
	 * @param UrlRemember        $urlRemember
	 * @param User               $user
	 * @param Request            $request
	 * @param PostRepository     $postRepository
	 * @param PaginatorInterface $paginator
	 *
	 * @param int                $postLimitPerPage
	 *
	 * @return Response
	 */
	public function bookmarks(UrlRemember $urlRemember, User $user, Request $request, PostRepository $postRepository, PaginatorInterface $paginator, int $postLimitPerPage): Response
	{
		$this->denyAccessUnlessGranted(BookmarksVoter::SHOW, $user, 'Authors can only see bookmarks!');

		$urlRemember->remember();

		$pagination = $paginator->paginate(
			$postRepository->userBookmarks($user->getId()),
			$request->query->getInt('page', 1),
			$postLimitPerPage
		);

		if ($request->isXmlHttpRequest())
		{
			return $this->render('post/_items.html.twig', ['pagination' => $pagination]);
		}

		return $this->render('user/bookmarks.html.twig', [
			'pagination' => $pagination,
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

}
