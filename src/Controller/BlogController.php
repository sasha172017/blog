<?php


namespace App\Controller;

use App\Repository\PostRepository;
use App\Services\UrlRemember;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BlogController
 * @package App\Controller
 */
class BlogController extends AbstractController
{
	/**
	 * @Route("/", name="blog_index")
	 * @param Request            $request
	 * @param PostRepository     $postRepository
	 * @param UrlRemember        $urlRemember
	 * @param PaginatorInterface $paginator
	 * @param int                $postLimitPerPage
	 *
	 * @return Response
	 */
	public function index(Request $request, PostRepository $postRepository, UrlRemember $urlRemember, PaginatorInterface $paginator, int $postLimitPerPage): Response
	{
		$urlRemember->remember();

		$pagination = $paginator->paginate(
			$postRepository->paginationQuery(),
			$request->query->getInt('page', 1),
			$postLimitPerPage
		);

		return $this->render($request->isXmlHttpRequest() ? 'post/_items.html.twig' : 'post/index.html.twig', ['pagination' => $pagination]);
	}
}
