<?php
declare(strict_types=1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


Abstract class BaseAbstractController extends AbstractController
{
    public function __construct() {
        #dd($_SERVER['HTTP_HOST']);
    }
}
