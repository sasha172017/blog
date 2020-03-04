<?php


namespace App\Controller;

use App\Services\PostPagination;
use App\Services\PostPaginationSortQuery;
use App\Services\UrlRemember;
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
	 * @param Request                 $request
	 * @param UrlRemember             $urlRemember
	 * @param PostPagination          $pagination
	 *
	 * @param PostPaginationSortQuery $paginationSortQuery
	 *
	 * @return Response
	 */
	public function index(Request $request, UrlRemember $urlRemember, PostPagination $pagination, PostPaginationSortQuery $paginationSortQuery): Response
	{
		$urlRemember->remember();

		$paginator = $pagination->pagination($paginationSortQuery->post());

		return $this->render($request->isXmlHttpRequest() ? 'post/_items.html.twig' : 'post/index.html.twig', ['pagination' => $paginator]);
	}
}
