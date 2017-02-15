<?php
namespace AppModule\Controller;

use AppModule\Model\ArticleDAO;
use Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ArticleController extends Controller
{

    public function indexAction(Request $request)
    {
        $session = new Session();
        $articleDAO = new ArticleDAO();
        $articles = $articleDAO->getAll();
        $request->attributes->set('articles', $articles);
        $request->setSession($session);

        return $this->render($request);
    }

    public function showAction(Request $request, $id)
    {
        $articleDAO = new ArticleDAO();
        $articles = $articleDAO->get($id);

        if(!$articles) {
            header('Location: http://localhost:8000');
            exit();
        }
        $request->attributes->set('articles', $articles);

        return $this->render($request);
    }

    public function postAction(Request $request)
    {
        return new JsonResponse(array(
            'title' => $request->get('title'),
            'content' => $request->get('content')
        ));
    }
}