<?php
/**
* This file is part of the IP-Trevise Application.
*
* PHP version 7.1
*
* (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
*
* @category Entity
*
* @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
* @copyright 2017 Cerema
* @license   CeCILL-B V1
*
* @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
*/

namespace App\Controller;

use App\Entity\Ip;
use App\Form\Type\IpType;
use App\Manager\IpManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use App\Bean\Factory\InformationFactory;
use App\Manager\MachineManager;
use App\Manager\NetworkManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
* Search CRUD Controller.
*
* @category Controller
*
* @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
* @license CeCILL-B V1
*
* @Route("search")
*/
class SearchController extends Controller
{
  /**
  * Limit of machine per page for listing.
  */
  const LIMIT_PER_PAGE = 25;




  /**
  * Lists of all machines or ips whose matched
  *
  * @Route("/", name="default_search_index")
  * @Method("GET")
  * @Security("is_granted('ROLE_READ_MACHINE')")
  *
  * @param Request $request
  *
  * @return Response
  */


  public function indexAction(Request $request)
  {
    //Retrieving all services
    $machineManager = $this->get(MachineManager::class);
    $paginator = $this->get('knp_paginator');
    $pagination = $paginator->paginate(
      $machineManager->getQueryBuilder(), /* queryBuilder NOT result */
      $request->query->getInt('page', 1)/*page number*/,
      self::LIMIT_PER_PAGE,
      ['defaultSortFieldName' => 'machine.label', 'defaultSortDirection' => 'asc']
    );


    $ipManager = $this->get(IpManager::class);
    $pagination2 = $paginator->paginate(
      $ipManager->getAll(), /* queryBuilder NOT result */
      $request->query->getInt('page', 1)/*page number*/,
      self::LIMIT_PER_PAGE,
      ['defaultSortFieldName' => 'ip.ip | ip', 'defaultSortDirection' => 'asc']
    );

    if (isset($_GET['search'])) { $search = $_GET['search']; } else { $search = "FailToCatch"; }

    return $this->render('@App/default/search/index.html.twig', [
      'pagination' => $pagination,
      'pagination2' => $pagination2,
      'result' => $search
    ]);

  }
}
?>
