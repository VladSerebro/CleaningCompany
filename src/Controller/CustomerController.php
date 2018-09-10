<?php

namespace App\Controller;

use App\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController extends AbstractController
{
    /**
     * @Route("/customer/identify", methods={"GET","POST"}, name="identify_customer")
     */
    public function identify(Request $request)
    {
        if($request->isMethod('POST'))
        {
            $phone_number = $request->request->get('phone_number');

            if(false) // if $phone_number is exist in db
            {
                //TODO return view create booking
            }
            else
            {
                $session = $this->get('session');
                $session->set('phone_number', $phone_number);

                return $this->redirectToRoute('new_customer');
            }
        }

        return $this->render('customer/identify.html.twig');
    }


    /**
     * @Route("/customer/new", methods={"GET","POST"}, name="new_customer")
     */
    public function new(Request $request)
    {
        $session = $this->get('session');
        $phone_number = $session->get('phone_number');

        if($request->isMethod('post'))
        {
            $params = $request->request;

            $customer = new Customer();
            $customer->setFirstName($params->get('first_name'));
            $customer->setLastName($params->get('last_name'));
            $customer->setPhoneNumber($phone_number);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();

            // TODO return redirect to create booking
        }

        return $this->render('customer/new.html.twig', [
            'phone_number' => $phone_number
        ]);
    }

}
