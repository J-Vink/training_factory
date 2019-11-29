<?php
namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\PersonRepository;
use App\Repository\TrainingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Training;
use App\Form\TrainingType;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BeheerderController
 * @package App\Controller
 * @Route("/admin", name="admin_")
 */
class BeheerderController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepageAction(TrainingRepository $trainingRepository): Response
    {
        return $this->render('beheerder/training/index.html.twig', [
            'trainings' => $trainingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/training", name="training_index", methods={"GET"})
     */
    public function trainingIndexAction(TrainingRepository $trainingRepository): Response
    {
        return $this->render('beheerder/training/index.html.twig', [
            'trainings' => $trainingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/training/new", name="training_new", methods={"GET","POST"})
     */
    public function newAction(Request $request): Response
    {
        $training = new Training();
        $form = $this->createForm(TrainingType::class, $training);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($training);
            $entityManager->flush();

            return $this->redirectToRoute('admin_training_index');
        }

        return $this->render('beheerder/training/new.html.twig', [
            'training' => $training,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/training/{id}", name="training_show", methods={"GET"})
     */
    public function showAction(Training $training): Response
    {
        return $this->render('beheerder/training/show.html.twig', [
            'training' => $training,
        ]);
    }

    /**
     * @Route("/training/{id}/edit", name="training_edit", methods={"GET","POST"})
     */
    public function editAction(Request $request, Training $training, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TrainingType::class, $training);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
                $uploadedFile->move(
                    $destination,
                    $newFilename
                );
                $training->setImageFilename($newFilename);
            }
            $em->persist($training);
            $em->flush();

            $this->addFlash('success', 'Training succesvol geupdatet!');

            return $this->redirectToRoute('admin_training_index');
        }
        return $this->render('beheerder/details.html.twig', [
            'trainingForm' => $form->createView(),
            'image' => $training->getImageFilename(),
        ]);
    }

    /**
     * @Route("/training/{id}", name="training_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Training $training): Response
    {
        if ($this->isCsrfTokenValid('delete'.$training->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($training);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_training_index');
    }

    /**
     * @Route("/leden", name="lid_index", methods={"GET"})
     */
    public function lidIndexAction(PersonRepository $personRepository): Response
    {
        return $this->render('beheerder/person/index.html.twig', [
            'people' => $personRepository->findAll(),
        ]);
    }

    /**
     * @Route("/instructeurs", name="instructeur_index", methods={"GET"})
     */
    public function instructeurIndexAction(PersonRepository $personRepository): Response
    {
        return $this->render('beheerder/person/index.html.twig', [
            'people' => $personRepository->findAll(),
        ]);
    }

    /**
     * @Route("/gebruiker/new", name="person_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($person);
            $entityManager->flush();

            return $this->redirectToRoute('admin_lid_index');
        }

        return $this->render('beheerder/person/new.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gebruiker/{id}", name="person_show", methods={"GET"})
     */
    public function showGebruikerAction(Person $person): Response
    {
        return $this->render('beheerder/person/show.html.twig', [
            'person' => $person,
        ]);
    }

    /**
     * @Route("/gebruiker/{id}/edit", name="person_edit", methods={"GET","POST"})
     */
    public function editGebruikerAction(Request $request, Person $person): Response
    {
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_lid_index');
        }

        return $this->render('beheerder/person/edit.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gebruiker/{id}", name="person_delete", methods={"DELETE"})
     */
    public function deleteGebruikerAction(Request $request, Person $person): Response
    {
        if ($this->isCsrfTokenValid('delete'.$person->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($person);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_lid_index');
    }
}