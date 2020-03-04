<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Post::class);
	}

	/**
	 * @param QueryBuilder $query
	 *
	 * @return QueryBuilder
	 */
	private function paginationDefaultSort(QueryBuilder $query): QueryBuilder
	{
		return $query->addOrderBy('p.updatedAt', 'DESC');
	}

	/**
	 * @return QueryBuilder
	 */
	public function paginationQuery(): QueryBuilder
	{
		$query = $this->createQueryBuilder('p');

		return $this->paginationDefaultSort($query);
	}

	/**
	 * @param QueryBuilder $query
	 *
	 * @return QueryBuilder
	 */
	private function queryTop(QueryBuilder $query): QueryBuilder
	{
		return $query->addSelect('p.rating + p.views as HIDDEN top')
			->having('top > 0');
	}

	/**
	 * @param QueryBuilder $query
	 *
	 * @return QueryBuilder
	 */
	private function queryCountComments(QueryBuilder $query): QueryBuilder
	{
		return $query->addSelect('COUNT(comments) as hidden countComments')
			->leftJoin('p.comments', 'comments')
			->groupBy('p.id');
	}

	/**
	 * @return QueryBuilder
	 */
	public function top(): QueryBuilder
	{
		return $this->queryTop($this->createQueryBuilder('p'));
	}

	/**
	 * @return QueryBuilder
	 */
	public function countComments(): QueryBuilder
	{
		return $this->queryCountComments($this->createQueryBuilder('p'));
	}

	/**
	 * @param int  $categoryId
	 *
	 * @param bool $defSort
	 *
	 * @return QueryBuilder
	 */
	public function categoryPosts(int $categoryId, bool $defSort = true): QueryBuilder
	{
		$query = $this->createQueryBuilder('p')
			->addSelect('p', 'c')
			->leftJoin('p.categories', 'c')
			->where('c.id = :categoryId')
			->setParameter('categoryId', $categoryId);

		return $defSort ? $this->paginationDefaultSort($query) : $query;
	}

	/**
	 * @param int $categoryId
	 *
	 * @return QueryBuilder
	 */
	public function categoryPostsTop(int $categoryId): QueryBuilder
	{
		return $this->queryTop($this->categoryPosts($categoryId, false));
	}

	/**
	 * @param int $categoryId
	 *
	 * @return QueryBuilder
	 */
	public function categoryPostsCountComments(int $categoryId): QueryBuilder
	{
		return $this->queryCountComments($this->categoryPosts($categoryId, false));
	}

	/**
	 * @param int  $userId
	 *
	 * @param bool $defSort
	 *
	 * @return QueryBuilder
	 */
	public function userPosts(int $userId, bool $defSort = true): QueryBuilder
	{
		$query = $this->createQueryBuilder('p')
			->addSelect('p', 'a')
			->leftJoin('p.author', 'a')
			->where('a.id = :userId')
			->setParameter('userId', $userId);

		return $defSort ? $this->paginationDefaultSort($query) : $query;
	}

	/**
	 * @param int $userId
	 *
	 * @return QueryBuilder
	 */
	public function userPostsTop(int $userId): QueryBuilder
	{
		return $this->queryTop($this->userPosts($userId, false));
	}

	/**
	 * @param int $userId
	 *
	 * @return QueryBuilder
	 */
	public function userPostsCountComments(int $userId): QueryBuilder
	{
		return $this->queryCountComments($this->userPosts($userId, false));
	}

	/**
	 * @param int  $userId
	 *
	 * @param bool $defSort
	 *
	 * @return QueryBuilder
	 */
	public function userBookmarks(int $userId, bool $defSort = true): QueryBuilder
	{
		$query = $this->createQueryBuilder('p')
			->addSelect('p', 'u')
			->leftJoin('p.users', 'u')
			->where('u.id = :userId')
			->setParameter('userId', $userId);

		return $defSort ? $this->paginationDefaultSort($query) : $query;
	}

	/**
	 * @param int $userId
	 *
	 * @return QueryBuilder
	 */
	public function userBookmarksPostsTop(int $userId): QueryBuilder
	{
		return $this->queryTop($this->userBookmarks($userId, false));
	}

	/**
	 * @param int $userId
	 *
	 * @return QueryBuilder
	 */
	public function userBookmarksPostsCountComments(int $userId): QueryBuilder
	{
		return $this->queryCountComments($this->userBookmarks($userId, false));
	}

}
