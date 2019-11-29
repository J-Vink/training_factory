<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        if ($this->getUser()) {
            if($security->isGranted('ROLE_ADMIN'))
            {
                return $this->redirectToRoute('admin_homepage');
            }
            if($security->isGranted('ROLE_INSTRUCTOR'))
            {
                return $this->redirectToRoute('medewerker_lesson_index');
            }
            if($security->isGranted('ROLE_USER'))
            {
                return $this->redirectToRoute('lid_homepage');
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
