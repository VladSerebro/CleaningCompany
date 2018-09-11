<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\City;
use App\Entity\Cleaner;
use App\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class BookingController extends AbstractController
{
    /**
     * @Route("/booking/create/select_params", methods={"GET","POST"}, name="select_params")
     */
    public function selectParams(Request $request)
    {
        if($request->isMethod('POST'))
        {
            $session = $this->get('session');

            $params = $request->request;

            $session->set('date', $params->get('date'));
            $session->set('city_id', $params->get('city'));
            $session->set('duration', $params->get('duration'));

            return $this->redirectToRoute('select_cleaner');
        }

        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();

        return $this->render('booking/selectParams.html.twig', ['cities' => $cities]);
    }


    /**
     * @Route("/booking/create/select_cleaner", methods={"GET","POST"}, name="select_cleaner")
     */
    public function selectCleaner()
    {
        $session = $this->get('session');

        $bookings = $this->getDoctrine()->getRepository(Booking::class)->getPossibleBookings();

        $strBegin = str_replace('T', ' ', $session->get('date'));
        $begin = \DateTime::createFromFormat('Y-m-d H:i', $strBegin);
        $end = clone $begin;
        $end->add(new \DateInterval('PT' . $session->get('duration') . 'H'));

        $city = $this->getDoctrine()->getRepository(City::class)->find($session->get('city_id'));
        $freeCleaners = $this->getDoctrine()->getRepository(Cleaner::class)
            ->findBy(['city' => $city]);

        foreach($bookings as $b)
        {
            $bookBegin = $b->getDate();
            $bookEnd = clone $bookBegin;
            $bookEnd->add(new \DateInterval('PT' . $b->getDuration() . 'H'));


            if( ($end >= $bookBegin && $end <= $bookEnd) || ($begin >= $bookBegin && $begin <= $bookEnd) ||
                ($bookEnd >= $begin && $bookEnd <= $end) || ($bookBegin >= $begin && $bookBegin <= $end))
            {
                $cleaner = $b->getCleaner();

                if(in_array($cleaner, $freeCleaners))
                {
                    unset($freeCleaners[array_search($cleaner, $freeCleaners)]);
                }
            }
        }

        if(count($freeCleaners) > 0)
        {
            return $this->render('booking/selectCleaner.html.twig',[
                'city' => $this->getDoctrine()->getRepository(City::class)->find($session->get('city_id')),
                'date' => $session->get('date'),
                'duration' => $session->get('duration'),
                'cleaners' => $freeCleaners
            ]);
        }
        else
        {
            //TODO: return 'no free cleaners'
            return $this->render('booking/noFreeCleaners.html.twig');
        }



    }

    /**
     * @Route("/booking/create/finish", methods={"GET", "POST"}, name="create_booking")
     */
    public function create(Request $request)
    {
        $session = $this->get('session');

        if($request->isMethod('post'))
        {
            $doctrine = $this->getDoctrine();

            $cleaner_id = $request->request->get('cleaner');
            $strDate = str_replace('T', ' ', $session->get('date'));

            $date = \DateTime::createFromFormat('Y-m-d H:i', $strDate);
            $cleaner = $doctrine->getRepository(Cleaner::class)->find($cleaner_id);
            $customer = $doctrine->getRepository(Customer::class)->find($session->get('customer_id'));

            $booking = new Booking();
            $booking->setDate($date);
            $booking->setDuration($session->get('duration'));
            $booking->setCleaner($cleaner);
            $booking->setCustomer($customer);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($booking);
            $entityManager->flush();

            $session->clear();

            return $this->render('booking/createSuccessfully.html.twig', [
                'booking' => $booking
            ]);
        }
        return $this->redirectToRoute('select_cleaner');
    }
}




























