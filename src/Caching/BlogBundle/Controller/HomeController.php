<?php

namespace Caching\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Caching\BlogBundle\Entity\Entry;
use Caching\BlogBundle\Entity\Route;
use Caching\BlogBundle\Entity\Point;
use Caching\BlogBundle\Form\Type\EntryType;
use Caching\BlogBundle\Form\Type\UploadType;
use JonsCaching\GpxReader\GpxReader;

class HomeController extends Controller
{
    public function indexAction()
    {
        $entry  = new Entry();
        $form   = $this->createForm(new EntryType());
        $upload = $this->createForm(new UploadType());
        $user   = $this->get('security.context')->getToken()->getUser();
        $em     = $this->getDoctrine()->getEntityManager();
        $routes = $em->getRepository('Caching\BlogBundle\Entity\Route')->fetchIds();
        
        $values = array(
            'user'      => $user,
            'form'      => $form->createView(),
            'upload'    => $upload->createView(),
            'routes'    => $routes,
        );
        
        return $this->render('CachingBlogBundle:Home:index.html.twig', $values);
    }
    
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR))
        {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        else
        {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        $values = array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
        
        return $this->render('CachingBlogBundle:Home:login.html.twig', $values);
    }
    
    public function createAction(Request $request)
    {
        // Make sure this function is called only with an ajax request
        if ($request->getMethod() == 'POST')
        {
            $entry  = new Entry();
            $form   = $this->createForm(new EntryType());
            $user   = $this->get('security.context')->getToken()->getUser();
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $formData   = $request->request->get('entry');

                $entry->setCreated(new \DateTime('now'));
                $entry->setTitle($formData['title']);
                $entry->setEntry($formData['entry']);
                $entry->setUser($user);
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($entry);
                $entry->setRoute(self::createRoute($request, $formData));
                $em->persist($entry);
                $em->flush();
            }
            else
            {
                $message = 'Failure: ' . $form->getErrors();
            }
        }

        return $this->redirect($this->generateUrl('home'));
    }
    
    public function createRoute(Request $request, $formData)
    {
        $file       = $request->files->get('entry');
        $routeArea  = $formData['route_area'];
        $filename   = time() . '.gpx';
        $dir        = $this->container->parameters['upload_dir'];

        $file['attachment']->move($dir, $filename);

        $gpxReader = new GpxReader($dir . $filename);
        $latLongs = $gpxReader->getLatLongs();

        $route = new Route();
        $route->setArea($routeArea);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($route);
        $em->flush();
        $id = $route->getId();
        $i  = 0;

        foreach($latLongs as $point)
        {
            $newPoint = new Point();
            $newPoint->setLatitude($point[0]);
            $newPoint->setLongitude($point[1]);
            $newPoint->setRoute($route);

            $em->persist($newPoint);

            $route->addPoint($newPoint);

            $i++;

            if ($i > 5)
            {
                $em->flush();
                $em->clear();
                $route = $em->getRepository('CachingBlogBundle:Route')->find($id);
                $i = 0;
            }
        }

        $em->persist($route);
        $em->flush();

        return $route;
    }
    
    public function viewRouteAction($id)
    {
        $route = $this->getDoctrine()->getEntityManager()
            ->getRepository('CachingBlogBundle:Route')
            ->find($id);

        $points     = array();
        $avgLat     = 0;
        $avgLong    = 0;
        $i          = 0;
        
        foreach ($route->getPoints()->getValues() as $key => $point)
        {
            $points[$key]['latitude']    = $point->getLatitude();
            $points[$key]['longitude']   = $point->getLongitude();
            $avgLat += $point->getLatitude();
            $avgLong += $point->getLongitude();
            $i++;
        }

        $values = array(
            'route'         => $route,
            'points'        => $points,
            'avgLat'        => $avgLat / $i,
            'avgLong'       => $avgLong / $i,
        );
        
        return $this->render('CachingBlogBundle:Home:view_route.html.twig', $values);
    }
}