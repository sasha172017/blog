<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends AbstractController
{
	/**
	 * @Route("/{nickname}/posts", name="user_posts", methods={"GET"})
	 * @param User $user
	 *
	 * @return Response
	 */
	public function posts(User $user): Response
	{
		return $this->render('blog/index.html.twig', [
			'posts' => $user->getPosts()
		]);
	}
}
