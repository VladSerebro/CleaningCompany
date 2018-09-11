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

            $foundCustomer = $this->getDoctrine()
                ->getRepository(Customer::class)
                ->findOneBy(['phone_number' => $phone_number]);

            $session = $this->get('session');

            if($foundCustomer)
            {
                $session->set('customer_id', $foundCustomer->getId());

                return $this->redirectToRoute('select_params');
            }
            else
            {
                $session->set('phone_number', $phone_number);

                return $this->redirectToRoute('new_customer');
            }
        }

        return $this->render('customer/identify.html.twig');
    }


    /**
     * @Route("/customer/new", methods={"GET","POST"}, name="new_customer")
     */
    public function new(Request $request, ValidatorInterface $validator)
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

            $errors = $validator->validate($customer);
            if(count($errors) > 0)
            {
                return $this->render('validation.html.twig', [
                    'errors' => $errors,
                    'entityName' => 'Customer',
                    'back' => '/customer/new'
                ]);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();

            $session->set('customer_id', $customer->getId());
            return $this->redirectToRoute('select_params');
        }

        return $this->render('customer/new.html.twig', [
            'phone_number' => $phone_number
        ]);
    }

}
