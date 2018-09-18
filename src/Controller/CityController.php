<?php

namespace App\Controller;

use App\Entity\City;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CityController extends AbstractController
{
    /**
     * @Route("/admin/city/index", methods={"GET", "POST"}, name="admin_cities_index")
     */
    public function index(Request $request)
    {
        if($request->isMethod('POST'))
        {
            $arr = $request->request->all();

            $manager = $this->getDoctrine()->getManager();
            $manager->getRepository(City::class)->resetActivity();

            $cities = $manager->getRepository(City::class)->findAll();

            foreach ($cities as $city)
            {
                dump($city->getId());
                dump($arr);
                if(key_exists($city->getId(), $arr))
                    $city->setIsActive(1);
            }

            $manager->flush();

            return $this->redirectToRoute('admin_booking_index');
        }

        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();

        return $this->render('city/index.html.twig', [
            'cities' => $cities
        ]);
    }

    /**
     * @Route("/admin/city/delete/{id}", methods={"DELETE"}, name="admin_cities_delete")
     */
    public function delete(Request $request, $id)
    {
        $city = $this->getDoctrine()->getRepository(City::class)
            ->find($id);

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($city);
        $manager->flush();

        $res = new Response();
        $res->send();
    }

    /**
     * @Route("/admin/city/create", methods={"GET", "POST"}, name="admin_cities_create")
     */
    public function add(Request $request, ValidatorInterface $validator)
    {
        if($request->isMethod('POST'))
        {
            $cityName = $request->request->get('city');

            $city = new City();
            $city->setName($cityName);
            $city->setIsActive(0);

            $arr_errors = $validator->validate($city);
            if (count($arr_errors) > 0)
            {
                return $this->showDanger($arr_errors);
            }
            else
            {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($city);
                $manager->flush();

                return $this->redirectToRoute('admin_cities_index');
            }
        }

        return $this->render('city/create.html.twig');
    }

    /**
     * @param array of errors
     *
     * @return Response
     */
    private function showDanger($arr_errors)
    {
        $errors = [];

        foreach($arr_errors as $error)
        {
            $errors[] = strtoupper($error->getPropertyPath()) . ' => ' . $error->getMessage();
        }
        return $this->render('validation.html.twig', [
            'errors' => $errors,
            'back' => '/admin/city/create'
        ]);
    }
}
