<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController extends AbstractController
{
	/**
	* @Route("/home", name="home")
	*/
	public function home()
	{
		return $this->render('patient/home.html.twig');
	}
	
	/**
	* @Route("/pharmacy", name="pharmacy")
	*/
	public function pharmacy()
	{
		return $this->render('pharmacy/pharmacy.html.twig');
	}
	
	
	/**
	* @Route("/menu", name="menu")
	*/
	public function menu(SessionInterface $session)
	{
		if($session->get('id')) //if user is logged in they can visit the page
		{
			return $this->render('patient/menu.html.twig');
		}
		else
		{
			return $this->redirectToRoute('home'); //if not logged in, they get brough back to the home page.
		}
	}
	
	/**
	* @Route("/staffzone", name="staffzone")
	*/
	public function staffzone(SessionInterface $session)
	{
		if($session->get('staff')) //if a staff member is logged in they can visit the page
		{
		    return $this->render('pharmacy/staffzone.html.twig');
		}
		else
		{
			if($session->get('id'))
			{
				return $this->redirectToRoute('menu'); //if a staff member is not logged in, but a patient is, the patient will be brought back to their menu
			}
			else
			{
				return $this->redirectToRoute('pharmacy');
			}				
		}
	}

	/**
	* @Route("/up", name="up")
	*/
	public function up(SessionInterface $session)
	{
		if($session->get('id')) //if user is logged in they can visit the page
		{
			return $this->render('patient/upload.html.twig');
		}
		else
		{
			return $this->redirectToRoute('home'); //if not logged in, they get brough back to the home page.
		}
	}
	
}
