<?php
/**
 * Created by PhpStorm.
 * User: marc
 * Date: 14/02/2017
 * Time: 15:40
 */

namespace AppModule\Controller;


use AppModule\Model\User;
use AppModule\Model\UserDAO;
use Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function signUpShowAction(Request $request)
    {
        return $this->render($request);
    }

    /**
     * @param Request $request
     */
    public function signUpAction(Request $request)
    {
        $data = $request->request->all();

        $userDAO = new UserDAO();
        $user = new User($data);

        $result = $userDAO->add($user);

        if($result) {
            $session = new Session();

            $session
                ->getFlashBag()
                ->set('success', 'Inscription terminée, merci.');

            self::signInAction($request);
        } else {
            $session = new Session();

            $session
                ->getFlashBag()
                ->set('error', 'Pseudo et/ou adresse email déjà pris');
            header('Location: /');
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function signInShowAction(Request $request)
    {
        return $this->render($request);
    }

    /**
     * @param Request $request
     */
    public function signInAction(Request $request)
    {
        $session = new Session();

        $data = $request->request->all();
        $data['password'] = sha1($data['password']);
        $userDAO = new UserDAO();
        $result = $userDAO->get($data);

        if (!$result) {
            $session
                ->getFlashBag()
                ->set('error', 'Mauvais pseudo ou mot de passe');
            header('Location: /');
        }

        $user = new User($result);
        $session->set('user', $user);

        if ($user->getRole() === 'administrateur') {
            $session
                ->getFlashBag()
                ->add('success', 'Vous êtes maintenant connectée en tant que administrateur');

            header('Location: /admin');
            exit();
        } else if (!empty($user->getRole())) {
            $session
                ->getFlashBag()
                ->add('success', 'Vous êtes maintenant connectée');

            header('Location: /');
            exit();
        }
    }

    /**
     * @param Request $request
     */
    public function signOutAction(Request $request)
    {
        $session = new Session();
        $session->clear();

        header('Location: /');
        exit();
    }
}