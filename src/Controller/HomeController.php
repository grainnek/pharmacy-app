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
	* @Route("/base", name="base")
	*/
	public function base()
	{
		return $this->render('patient_base.html.twig');
	}
	
		/**
	* @Route("/staffbase", name="staffbase")
	*/
	public function staffbase()
	{
		return $this->render('staff_base.html.twig');
	}

}
