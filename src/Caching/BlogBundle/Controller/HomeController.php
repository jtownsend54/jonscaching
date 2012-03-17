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
        $form       = $this->createForm(new EntryType());
        $user       = $this->get('security.context')->getToken()->getUser();
        $em         = $this->getDoctrine()->getEntityManager();
        $entries    = $em->getRepository('Caching\BlogBundle\Entity\Entry')->fetchAll();

        return $this->render('CachingBlogBundle:Home:index.html.twig', array(
            'user'      => $user,
            'form'      => $form->createView(),
            'entries'   => $entries,
        ));
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
        
        return $this->render('CachingBlogBundle:Home:login.html.twig', array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
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

                $em->flush();
            }
            else
            {
                var_dump($form->getErrors());
                die;
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

        $gpxReader  = new GpxReader($dir . $filename);
        $latLongs   = $gpxReader->getLatLongs();

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

            if ($i > 1500)
            {

                $em->flush();
                $em->clear();
                //echo 2;
                //die;
                $route  = $em->getRepository('CachingBlogBundle:Route')->find($id);
                //$i      = 0;
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

        return $this->render('CachingBlogBundle:Home:view_route.html.twig', array(
            'route'         => $route,
            'points'        => $points,
            'avgLat'        => $avgLat / $i,
            'avgLong'       => $avgLong / $i,
        ));
    }

    public function uploadPhotoAction(Request $request)
    {
        $file       = $request->files->get('file');
        $entryid    = $request->request->get('entry_id');
        $entry      = $this->getDoctrine()->getEntityManager()
                        ->getRepository('CachingBlogBundle:Entry')
                        ->find($entryid);

        // Clone $file to $thumb

        // Resize $thumb to fit in masonry

        // move $thumb into /images/route/{route}/thumbs/

        // move $file into /images/route/{route}/fulls/
        $file->move($this->container->parameters['upload_dir'] . $entry->getRoute()->getFolderName(), $file->getClientOriginalName());

        $json = array(
            'success'   => 1,
            'entry_id'  => $entryid,
        );

        return $this->render('CachingBlogBundle:Home:json.html.twig', array(
            'json' => json_encode($json),
        ));
    }
    
}