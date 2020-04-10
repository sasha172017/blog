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
	 * @param int  $tagId
	 *
	 * @param bool $defSort
	 *
	 * @return QueryBuilder
	 */
	public function tagPosts(int $tagId, bool $defSort = true): QueryBuilder
	{
		$query = $this->createQueryBuilder('p')
			->addSelect('p', 't')
			->leftJoin('p.tags', 't')
			->where('t.id = :tagId')
			->setParameter('tagId', $tagId);

		return $defSort ? $this->paginationDefaultSort($query) : $query;
	}

	/**
	 * @param int $tagId
	 *
	 * @return QueryBuilder
	 */
	public function tagPostsTop(int $tagId): QueryBuilder
	{
		return $this->queryTop($this->tagPosts($tagId, false));
	}

	/**
	 * @param int $tagId
	 *
	 * @return QueryBuilder
	 */
	public function tagPostsCountComments(int $tagId): QueryBuilder
	{
		return $this->queryCountComments($this->tagPosts($tagId, false));
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

	/**
	 * @param string $q
	 *
	 * @return string|string[]
	 */
	private function preparationSearchQuery(string $q)
	{
		return str_replace(['@', '*', '(', ')', '-', '+', '=', '<?>', '>'], ' ', $q);
	}

	/**
	 * @param string $q
	 *
	 * @return QueryBuilder
	 */
	public function search(string $q): QueryBuilder
	{
		$query = $this->createQueryBuilder('p')
			//->addSelect('MATCH_AGAINST(p.title, p.summary, p.content) AGAINST(:q boolean) as hidden relev')
			//->andWhere('MATCH_AGAINST(p.title, p.summary, p.content) AGAINST(:q boolean) > 0')
			->andWhere('MATCH_AGAINST(p.title, p.summary, p.content) AGAINST(:q boolean) > 0')
			->setParameter('q', $q);

		return $this->paginationDefaultSort($query);
	}

	/**
	 * @param string $q
	 * @param int    $tagId
	 *
	 * @return QueryBuilder
	 */
	public function searchWithTag(string $q, int $tagId): QueryBuilder
	{
		$query = $this->search($q);

		return $query->addSelect('p', 't')
			->leftJoin('p.tags', 't')
			->andWhere('t.id = :tagId')
			->setParameter('tagId', $tagId);
	}

	/**
	 * @param string $q
	 * @param int    $userId
	 *
	 * @return QueryBuilder
	 */
	public function searchWithUser(string $q, int $userId): QueryBuilder
	{
		$query = $this->search($q);

		return $query->addSelect('p', 'u')
			->leftJoin('p.author', 'u')
			->andWhere('u.id = :userId')
			->setParameter('userId', $userId);
	}

	/**
	 * @param string $q
	 * @param int    $userId
	 *
	 * @return QueryBuilder
	 */
	public function searchInBookmarks(string $q, int $userId): QueryBuilder
	{
		$query = $this->search($q);

		return $query->addSelect('p', 'u')
			->leftJoin('p.users', 'u')
			->andWhere('u.id = :userId')
			->setParameter('userId', $userId);
	}

}
