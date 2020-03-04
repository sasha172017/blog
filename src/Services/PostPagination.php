<?php


namespace App\Services;

use App\Repository\PostRepository;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class PostPagination
 * @package App\Services
 */
class PostPagination
{
	private const OPTIONS = [
		'wrap-queries'         => true,
		'defaultSortDirection' => 'desc'
	];

	/**
	 * @var RequestStack
	 */
	private $requestStack;

	/**
	 * @var PostRepository
	 */
	private $postRepository;

	/**
	 * @var PaginatorInterface
	 */
	private $paginator;

	/**
	 * @var int
	 */
	private $postLimitPerPage;

	/**
	 * PostPagination constructor.
	 *
	 * @param RequestStack       $requestStack
	 * @param PostRepository     $postRepository
	 * @param PaginatorInterface $paginator
	 * @param int                $postLimitPerPage
	 */
	public function __construct(RequestStack $requestStack, PostRepository $postRepository, PaginatorInterface $paginator, int $postLimitPerPage)
	{
		$this->requestStack     = $requestStack;
		$this->postRepository   = $postRepository;
		$this->paginator        = $paginator;
		$this->postLimitPerPage = $postLimitPerPage;
	}

	/**
	 * @return QueryBuilder
	 */
	private function defaultQuery(): QueryBuilder
	{
		return $this->postRepository->paginationQuery();
	}

	/**
	 * @param QueryBuilder|null $query
	 * @param array             $options
	 *
	 * @return PaginationInterface
	 */
	public function pagination(QueryBuilder $query = null, array $options = []): PaginationInterface
	{
		return $this->paginator->paginate(
			$query ?? $this->defaultQuery(),
			$this->requestStack->getCurrentRequest()->query->getInt('page', 1),
			$this->postLimitPerPage,
			array_merge(self::OPTIONS, $options)
		);
	}
}
