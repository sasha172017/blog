<?php


namespace App\Services;


use App\Repository\PostRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

class PostPaginationSortQuery
{
	/**
	 * @var string
	 */
	private $sort;

	/**
	 * @var PostRepository
	 */
	private $postRepository;

	/**
	 * PostPaginationSortQuery constructor.
	 *
	 * @param RequestStack   $requestStack
	 * @param PostRepository $postRepository
	 */
	public function __construct(RequestStack $requestStack, PostRepository $postRepository)
	{
		$this->sort           = $requestStack->getCurrentRequest()->query->get('sort');
		$this->postRepository = $postRepository;
	}

	/**
	 * @return QueryBuilder|null
	 */
	public function post(): ?QueryBuilder
	{
		switch ($this->sort)
		{
			case 'top':
				$query = $this->postRepository->top();
				break;
			case 'countComments':
				$query = $this->postRepository->countComments();
				break;
			default:
				$query = $this->postRepository->paginationQuery();
				break;
		}

		return $query;
	}

	public function category(int $categoryId): ?QueryBuilder
	{
		switch ($this->sort)
		{
			case 'top':
				$query = $this->postRepository->categoryPostsTop($categoryId);
				break;
			case 'countComments':
				$query = $this->postRepository->categoryPostsCountComments($categoryId);
				break;
			default:
				$query = $this->postRepository->categoryPosts($categoryId);
				break;
		}

		return $query;
	}

	/**
	 * @param int $userId
	 *
	 * @return QueryBuilder
	 */
	public function user(int $userId): QueryBuilder
	{
		switch ($this->sort)
		{
			case 'top':
				$query = $this->postRepository->userPostsTop($userId);
				break;
			case 'countComments':
				$query = $this->postRepository->userPostsCountComments($userId);
				break;
			default:
				$query = $this->postRepository->userPosts($userId);
				break;
		}

		return $query;
	}

	/**
	 * @param int $userId
	 *
	 * @return QueryBuilder
	 */
	public function userBookmarks(int $userId): QueryBuilder
	{
		switch ($this->sort)
		{
			case 'top':
				$query = $this->postRepository->userBookmarksPostsTop($userId);
				break;
			case 'countComments':
				$query = $this->postRepository->userBookmarksPostsCountComments($userId);
				break;
			default:
				$query = $this->postRepository->userBookmarks($userId);
				break;
		}

		return $query;
	}

}
