<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\City;
use App\Entity\Cleaner;
use App\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class BookingController
 * @package App\Controller
 */
class BookingController extends AbstractController
{
    /**
     * @Route("/booking/create/select_params", methods={"GET","POST"}, name="select_params")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function selectParams(Request $request)
    {
        if($request->isMethod('POST'))
        {
            $params = $request->request;

            // validation (not blank)
            $errors = [];
            foreach($params as $key => $value)
            {
                if(empty($value))
                    $errors[] = strtoupper($key) .  " => This value should not be blank";
            }
            if(count($errors) > 0)
                return $this->render('validation.html.twig', [
                    'errors' => $errors,
                    'back' => '/booking/create/select_params'
                ]);

            $session = $this->get('session');

            $session->set('date', $params->get('date'));
            $session->set('city_id', $params->get('city'));
            $session->set('duration', $params->get('duration'));


            return $this->redirectToRoute('select_cleaner');
        }

        $cities = $this->getDoctrine()->getRepository(City::class)
            ->findBy(['is_active' => '1']);

        return $this->render('booking/selectParams.html.twig', ['cities' => $cities]);
    }

    /**
     * @Route("/booking/create/select_cleaner", methods={"GET","POST"}, name="select_cleaner")
     *
     * @return Response
     * @throws \Exception
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
            return $this->render('booking/noFreeCleaners.html.twig');
        }
    }

    /**
     * @Route("/booking/create/finish", methods={"GET", "POST"}, name="create_booking")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
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


//======= admin/ ========//

    /**
     * @Route("/admin/booking/index", name="admin_booking_index")
     *
     * @return Response
     */
    public function index()
    {
        $doctrine = $this->getDoctrine();

        $bookings = $doctrine->getRepository(Booking::class)->findAllSorted();

        return $this->render('booking/index.html.twig', [
            'bookings' => $bookings,
            'tableName' => 'All bookings'
        ]);
    }

    /**
     * @Route("/admin/booking/index/actual", name="admin_booking_index_actual")
     *
     * @return Response
     */
    public function indexActual()
    {
        $doctrine = $this->getDoctrine();

        $bookings = $doctrine->getRepository(Booking::class)->getPossibleBookings();

        return $this->render('booking/index.html.twig', [
            'bookings' => $bookings,
            'tableName' => 'Actual bookings'
        ]);
    }

    /**
     * @Route("/admin/booking/delete/{id}", methods={"DELETE"}, name="admin_booking_delete")
     *
     * @param Request $request
     * @param $id
     */
    public function delete(Request $request, $id)
    {
        $booking = $this->getDoctrine()->getRepository(Booking::class)
            ->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($booking);
        $entityManager->flush();

        $response = new Response();
        $response->send();
    }

    /**
     * @Route("/admin/booking/edit/{id}", methods={"GET", "POST"}, name="admin_booking_edit")
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function edit(Request $request, ValidatorInterface $validator, $id)
    {
        if($request->isMethod('POST'))
        {
            $entityManager = $this->getDoctrine()->getManager();
            $booking = $entityManager->getRepository(Booking::class)->find($id);

            $booking->setCleaner(
                $this->getDoctrine()->getRepository(Cleaner::class)->find($request->request->get('cleaner'))
            );

            $strDate = $request->request->get('date');
            $strDate = str_replace('T', ' ', $strDate);
            $date = \DateTime::createFromFormat('Y-m-d H:i', $strDate);

            if($date){
                $booking->setDate($date);
            }

            $booking->setDuration($request->request->get('duration'));

            $arr_errors = $validator->validate($booking);

            if (count($arr_errors) > 0)
            {
                return $this->showDanger($arr_errors, '/admin/booking/edit/' . $id);
            }
            else
            {
                $entityManager->flush();
                return $this->redirectToRoute('admin_booking_index');
            }
        }

        $booking = $this->getDoctrine()->getRepository(Booking::class)->find($id);

        $date = $booking->getDate()->format('Y-m-d H:i:s');
        $date = str_replace(' ', 'T', $date);

        $cleaners = $this->getDoctrine()->getRepository(Cleaner::class)
            ->findBy([
                'city' => $booking->getCleaner()->getCity()
            ]);

        return $this->render('booking/edit.html.twig', [
            'booking'   => $booking,
            'date'      => $date,
            'cleaners'  => $cleaners
        ]);
    }

    /**
     * Redirect to page with errors
     *
     * @param array $arr_errors
     * @param string $back
     * @return Response
     */
    private function showDanger($arr_errors, string $back)
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
