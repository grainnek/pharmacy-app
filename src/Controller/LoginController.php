<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\PatientDetails;
use App\Entity\Admin;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class LoginController extends AbstractController
{
	
	 // add in the session bit
	private $session;
		
	public function __construct(SessionInterface $session) {
		$this->session = $session;
	}
	
    /**
     * @Route("/login", name="login")
     */
    public function index(SessionInterface $session)
    {
		$request = Request::createFromGlobals();
		//get variables
		$username = $request->request->get('username', 'none');
		$password = $request->request->get('password', 'none');
		$role = $request->request->get('role', 'none');
		
		if($role == "customer")
		{
			//prepare the patient details table for queries
		    $patient = $this->getDoctrine()->getRepository(PatientDetails::class);
			
			//narrows in one row of the database
		    $person = $patient->findOneBy(['username' => $username, 'password' => $password]);
			
			//if there is a result
			if($person)
			{
				//set variables for use in the session
				$id = $person->getId();
				$fname = $person->getFname();
				$lname = $person->getLname();
				$email = $person->getEmail();
			
				//set the session variables for use in upload feature and email notifications
				$session->set('id', $id);
				$session->set('fname', $fname);
				$session->set('lname', $lname);
				$session->set('email', $email);
				
				return new Response("Welcome ".$session->get('email'));
			}
			//login has failed
			else 
			{
				return new Response("fail");
			}
		}
		else if($role == "staff")
		{
			$admin = $this->getDoctrine()->getRepository(Admin::class);
			
		    $staff = $admin->findOneBy(['username' => $username, 'password' => $password]);
			
			$id = $staff->getId();
			$name = $staff->getName();
			
			$session->set('staff', $id);
			$session->set('sname', $name);
			
			return new Response("Welcome!");
		}
		
    }
}
