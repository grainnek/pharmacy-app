<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\PatientDetails;
use App\Entity\Prescriptions;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class PatientController extends AbstractController
{
    /**
     * @Route("/history", name="history")
     */
    public function history(SessionInterface $session) //retreives single patient prescription history
    {
		if($session->get('id')) //if user is logged in they can visit the page
		{			
			$patient = $this->getDoctrine()->getRepository(Prescriptions::class);
		
			$id = $session->get('id');
		
			$history = $patient->findBy(['patient_id' => $id]);
			
			return $this->render('patient/history.html.twig', array('history' => $history));
		}
		else
		{
			return $this->redirectToRoute('home');
		}		
    }
	
	/**
     * @Route("/profile", name="profile")
     */
    public function profile(SessionInterface $session) //retreives single patient prescription history
    {		
		$patient = $this->getDoctrine()->getRepository(PatientDetails::class);
		
		$id = $session->get('id');
		
		$details = $patient->findOneBy(['id' => $id]);		
		
		return $this->render('patient/profile.html.twig', array('details' => $details));
    }	
	
	/**
     * @Route("/logout", name="logout")
     */
    public function logout(SessionInterface $session) //retreives single patient prescription history
    {		
		$name = $session->get('fname'); //get the logged in users name before session is cleared
		$session->clear();
							
		return new Response('Goodbye '.$name.'. See you next time.');
    }	
}
