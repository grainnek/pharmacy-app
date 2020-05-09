<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\PatientDetails;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function index()
    {
		$request = Request::createFromGlobals();
		//get variables
		$fname = $request->request->get('fname', 'none');
		$lname = $request->request->get('lname', 'none');
		$sex = $request->request->get('sex', 'none');
		$dob = $request->request->get('dob', 'none');
		$address1 = $request->request->get('address1', 'none');
		$address2 = $request->request->get('address2', 'none');
		$town = $request->request->get('town', 'none');
		$county = $request->request->get('county', 'none');
		$nationality = $request->request->get('nationality', 'none');
		$doctor = $request->request->get('doctor', 'none');
		$scheme = $request->request->get('scheme', 'none');
		$schemeid = $request->request->get('schemeid', 'none');
		$allergies = $request->request->get('allergies', 'none');
		$phone = $request->request->get('phone', 'none');
		$email = $request->request->get('email', 'none');
		$user = $request->request->get('user', 'none');
		$password = $request->request->get('password', 'none');
		
		//preparing database manager
		$entityManager = $this->getDoctrine()->getManager();
		
		//create a new row in patient details
		$patient = new PatientDetails();
		
		//assign values to db columns
		$patient->setFname($fname);
		$patient->setLname($lname);
		$patient->setSex($sex);
		$patient->setDob($dob);
		$patient->setAddress1($address1);
		$patient->setAddress2($address2);
		$patient->setTown($town);
		$patient->setCounty($county);
		$patient->setNationality($nationality);
		$patient->setDoctor($doctor);
		$patient->setScheme($scheme);
		$patient->setSchemeid($schemeid);
		$patient->setAllergies($allergies);
		$patient->setEmail($email);
		$patient->setPhone($phone);
		$patient->setUsername($user);
		$patient->setPassword($password);
		
		//finalise patient entries
		$entityManager->persist($patient);
		
		//commiting to the db
		$entityManager->flush();
		
        return new Response("Thank you for registering " . $fname);
    }
}
?>