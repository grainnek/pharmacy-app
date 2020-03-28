<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\PatientDetails;


class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function index()
    {
		$request = Request::createFromGlobals();
		//get variables
		$username = $request->request->get('username', 'none');
		$password = $request->request->get('password', 'none');
		
		$patient = $this->getDoctrine()->getRepository(PatientDetails::class);
			
		$person = $patient->findOneBy(['username' => $username, 'password' => $password]);
			
		return new Response("Welcome ".$person->getFname());
		
    }
}
