<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
	/**
	* @Route("/home", name="home")
	*/
	public function home()
	{
		return $this->render('home.html.twig');
	}
	
	/**
	* @Route("/pharmacy", name="pharmacy")
	*/
	public function pharmacy()
	{
		return $this->render('pharmacy.html.twig');
	}
	
	
	/**
	* @Route("/landing", name="landing")
	*/
	public function landing()
	{
		return $this->render('landing.html.twig');
	}

}
