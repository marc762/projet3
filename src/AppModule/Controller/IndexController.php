<?php

namespace AppModule\Controller;

use AppModule\Model\ArticleDAO;
use Core\Controller\Controller;
use Core\Database\Database;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class IndexController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render($request);
    }
}