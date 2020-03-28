<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\PatientDetails;
use App\Entity\Admin;


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
		$role = $request->request->get('role', 'none');
		
		if($role == "customer")
		{
		    $patient = $this->getDoctrine()->getRepository(PatientDetails::class);
			
		    $person = $patient->findOneBy(['username' => $username, 'password' => $password]);
			
			return new Response("Welcome ".$person->getFname());
		}
		else if($role == "staff")
		{
			$admin = $this->getDoctrine()->getRepository(Admin::class);
			
		    $staff = $admin->findOneBy(['username' => $username, 'password' => $password]);
			
			return new Response("Welcome!");
		}
		
    }
}
