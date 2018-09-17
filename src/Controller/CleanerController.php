<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Cleaner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CleanerController extends AbstractController
{
    /**
     * @Route("/admin/cleaner/index", methods={"GET"}, name="admin_cleaner_index")
     */
    public function index()
    {
        $cleaners = $this->getDoctrine()->getRepository(Cleaner::class)->findAll();

        return $this->render('cleaner/index.html.twig', [
            'cleaners' => $cleaners
        ]);
    }

    /**
     * @Route("/admin/cleaner/delete/{id}", methods={"DELETE"}, name="admin_cleaner_delete")
     */
    public function delete($id)
    {
        $cleaner = $this->getDoctrine()->getRepository(Cleaner::class)
            ->find($id);

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($cleaner);
        $manager->flush();

        $res = new Response();
        $res->send();
    }

    /**
     * @Route("/admin/cleaner/create", methods={"GET", "POST"}, name="admin_cleaner_create")
     */
    public function create(Request $request)
    {
        if($request->isMethod('POST'))
        {
            $city = $this->getDoctrine()->getRepository(City::class)
                ->find($request->request->get('city'));

            $cleaner = new Cleaner();
            $cleaner->setFirstName($request->request->get('firstName'));
            $cleaner->setLastName($request->request->get('lastName'));
            $cleaner->setCity($city);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($cleaner);
            $manager->flush();

            return $this->redirectToRoute('admin_cleaner_index');
        }

        $cities = $this->getDoctrine()->getRepository(City::class)
            ->findBy(['is_active' => '1']);
        return $this->render('cleaner/create.html.twig', [
            'cities' => $cities
        ]);
    }
}
