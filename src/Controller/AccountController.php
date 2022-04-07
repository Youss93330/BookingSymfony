<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher une page connexion
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig',[
            'hasError' => $error!==null,
            'username' => $username
        ]);
    }


    /**
     * Permet de se deconnecter
     * @Route("/logout",name="account_logout")
     *
     * @return void
     */
    public function logout(){
        // tout se passe via le fichier security.yaml
    }


    /**
     * Permet d'afficher une page s'inscrire
     * @Route("/register",name="account_register")
     *
     * @return Response
     */
    public function register(Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $manager){

        $user = new User();

        $form = $this->createForm(RegistrationType::class,$user);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $hash = $encoder->encodePassword($user,$user->getHash());

            // on modifie le mot de passe avec le setter

            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success","Votre compte a bien été crée");

            return $this->redirectToRoute("account_login");
        }

        return $this->render("account/register.html.twig",[
            'form'=>$form->createView()
        ]);
    }

    
    /**
     * Modification du profil utilisateur
     *
     * @Route("/account/profile",name="account_profile")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function profile(Request $request,EntityManagerInterface $manager){

        $user = $this->getUser();

        $form = $this->createForm(AccountType::class,$user);
        $form->handlerequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($user);

            $manager->flush();

            $this->addFlash("success","Les informations de votre profil ont bien été modifiées");
        }

        return $this->render('account/profile.html.twig',[
            'form'=>$form->createView()
        ]);
    }


    /**
     * Permet la modification du mot de passe
     * @Route("/account/password-update",name="account_password")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function updatePassword(Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $manager){

        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class,$passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Mot de passe actuel n'est pas le bon
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getHash())){

            // message d'erreur
            // $this->addFlash("warning","Votre mot de passe actuel est incorrect");

            $form->get('oldPassword')->addError(new FormError("Votre mot de passe est incorrect"));

            }else{

            // on recupere le nouveau mot de passe
            $newPassword = $passwordUpdate->getNewPassword();

            // on crypte le nouveau mot de passe
            $hash = $encoder->encodePassword($user,$newPassword);

            // on modifie le nouveau mdp dans le setter
            $user->setHash($hash);

            // on enregistre
            $manager->persist($user);

            $manager->flush();

            // on ajoute un message
            $this->addFlash("success","Votre nouveau mot de passe a bien été enregistré");

            // on redirige
            return $this->redirectToRoute('account_profile');

            }

        }

        return $this->render('account/password.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    
    /**
     * Permet d'afficher la page mon compte
     * @Route("/account",name="account_home")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function myAccount(){

        return $this->render("user/index.html.twig",['user'=>$this->getUser()]);
    }
}
