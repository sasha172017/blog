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
	private function paginationSort(QueryBuilder $query): QueryBuilder
	{
		return $query->orderBy('p.updatedAt', 'DESC');
	}

	/**
	 * @return QueryBuilder
	 */
	public function paginationQuery(): QueryBuilder
	{
		$query = $this->createQueryBuilder('p');

		return $this->paginationSort($query);
	}

	/**
	 * @param int $categoryId
	 *
	 * @return QueryBuilder
	 */
	public function categoryPosts(int $categoryId): QueryBuilder
	{
		$query = $this->createQueryBuilder('p')
			->addSelect('p', 'c')
			->leftJoin('p.categories', 'c')
			->where('c.id = :categoryId')
			->setParameter('categoryId', $categoryId);

		return $this->paginationSort($query);
	}

	/**
	 * @param int $authorId
	 *
	 * @return QueryBuilder
	 */
	public function userPosts(int $authorId): QueryBuilder
	{
		$query = $this->createQueryBuilder('p')
			->addSelect('p', 'a')
			->leftJoin('p.author', 'a')
			->where('a.id = :authorId')
			->setParameter('authorId', $authorId);

		return $this->paginationSort($query);
	}

	/**
	 * @param int $userId
	 *
	 * @return QueryBuilder
	 */
	public function userBookmarks(int $userId): QueryBuilder
	{
		$query = $this->createQueryBuilder('p')
			->addSelect('p', 'u')
			->leftJoin('p.users', 'u')
			->where('u.id = :userId')
			->setParameter('userId', $userId)
		;

		return $this->paginationSort($query);
	}

}
