<?php


namespace App\Services;

use App\Repository\PostRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class PostPaginationSortQuery
 * @package App\Services
 */
class PostPaginationSortQuery
{
	private const PARAM_TOP = 'top';
	private const PARAM_COMMENTS = 'countComments';

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
			case self::PARAM_TOP:
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

	/**
	 * @param int $tagId
	 *
	 * @return QueryBuilder|null
	 */
	public function tag(int $tagId): ?QueryBuilder
	{
		switch ($this->sort)
		{
			case self::PARAM_TOP:
				$query = $this->postRepository->tagPostsTop($tagId);
				break;
			case self::PARAM_COMMENTS:
				$query = $this->postRepository->tagPostsCountComments($tagId);
				break;
			default:
				$query = $this->postRepository->tagPosts($tagId);
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
			case self::PARAM_TOP:
				$query = $this->postRepository->userPostsTop($userId);
				break;
			case self::PARAM_COMMENTS:
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
			case self::PARAM_TOP:
				$query = $this->postRepository->userBookmarksPostsTop($userId);
				break;
			case self::PARAM_COMMENTS:
				$query = $this->postRepository->userBookmarksPostsCountComments($userId);
				break;
			default:
				$query = $this->postRepository->userBookmarks($userId);
				break;
		}

		return $query;
	}

}
