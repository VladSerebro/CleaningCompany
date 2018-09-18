<?php

namespace App\Controller;

use App\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController extends AbstractController
{
    /**
     * @Route("/customer/identify", methods={"GET","POST"}, name="identify_customer")
     */
    public function identify(Request $request, ValidatorInterface $validator)
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

            $arr_errors = $validator->validate($customer);
            if (count($arr_errors) > 0)
            {
                return $this->showDanger($arr_errors, '/customer/new');
            }
            else
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($customer);
                $entityManager->flush();

                $session->set('customer_id', $customer->getId());
                return $this->redirectToRoute('select_params');
            }
        }

        return $this->render('customer/new.html.twig', [
            'phone_number' => $phone_number
        ]);
    }

    /**
     * @Route("/admin/customer/index", methods={"GET"}, name="admin_customer_index")
     */
    public function index()
    {
        $customers = $this->getDoctrine()->getRepository(Customer::class)
            ->findAll();

        return $this->render('customer/index.html.twig', [
            'customers' => $customers
        ]);
    }

    /**
     * @Route("/admin/customer/delete/{id}", methods={"DELETE"}, name="admin_customer_delete")
     */
    public function delete($id)
    {
        $customer = $this->getDoctrine()->getRepository(Customer::class)
            ->find($id);

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($customer);
        $manager->flush();

        $res = new Response();
        $res->send();
    }

    /**
     * @Route("/admin/customer/create", methods={"GET", "POST"}, name="admin_customer_create")
     */
    public function create(Request $request, ValidatorInterface $validator)
    {
        if($request->isMethod('POST'))
        {
            $customer = new Customer();
            $customer->setFirstName($request->request->get('firstName'));
            $customer->setLastName($request->request->get('lastName'));
            $customer->setPhoneNumber($request->request->get('phoneNumber'));

            $arr_errors = $validator->validate($customer);
            if (count($arr_errors) > 0)
            {
                return $this->showDanger($arr_errors, '/admin/customer/create');
            }
            else
            {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($customer);
                $manager->flush();

                return $this->redirectToRoute('admin_customer_index');
            }
        }

        return $this->render('customer/create.html.twig');
    }

    /**
     * @Route("/admin/customer/edit/{id}", methods={"GET", "POST"}, name="admin_customer_edit")
     */
    public function edit(Request $request, ValidatorInterface $validator, $id)
    {
        $customer = $this->getDoctrine()->getRepository(Customer::class)->find($id);

        if($request->isMethod('POST'))
        {
            $manager = $this->getDoctrine()->getManager();
            $customer = $manager->getRepository(Customer::class)->find($id);

            $customer->setFirstName($request->request->get('firstName'));
            $customer->setLastName($request->request->get('lastName'));
            $customer->setPhoneNumber($request->request->get('phoneNumber'));

            $arr_errors = $validator->validate($customer);
            if (count($arr_errors) > 0)
            {
                return $this->showDanger($arr_errors, '/admin/customer/edit/' . $id);
            }
            else
            {
                $manager->flush();
                return $this->redirectToRoute('admin_customer_index');
            }
        }

        return $this->render('customer/edit.html.twig', [
            'customer' => $customer
        ]);
    }

    /**
     * @param array of errors
     *
     * @return Response
     */
    private function showDanger($arr_errors, $back)
    {
        $errors = [];

        foreach($arr_errors as $error)
        {
            $errors[] = strtoupper($error->getPropertyPath()) . ' => ' . $error->getMessage();
        }
        return $this->render('validation.html.twig', [
            'errors' => $errors,
            'back' => $back
        ]);
    }
}
