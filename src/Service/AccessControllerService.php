<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AccessControllerService extends AbstractController
{

	public function __construct(
		private EntityManagerInterface $emi,
	) {
	}

	public function IsConnected(string $message)
	{
		if (!$this->isGranted('ROLE_USER')) {
			$this->addFlash('danger', $message);
			return true;
		}
	}

	public function IsAdmin(string $message)
	{
		if (!$this->isGranted('ROLE_ADMIN')) {
			$this->addFlash('danger', $message);
			return true;
		}
	}

	public function IdIsCorrect(int $id, $entity, string $message)
	{
		if (!$this->emi->getRepository($entity)->find($id)) {
			$this->addFlash('danger', $message);
			return true;
		}
	}

	public function EditAccessController($element, string $message)
	{
		if ($element->getAuthor() != $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
			$this->addFlash('danger', $message);
			return true;
		}
	}

	public function EditUserProfileController($element, string $message)
	{
		if ($element != $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
			$this->addFlash('danger', $message);
			return true;
		}
	}

	public function DeleteAccessController($element, string $message)
	{
		if ($element->getAuthor() != $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
			$this->addFlash('danger', $message);
			return true;
		}
	}
}
