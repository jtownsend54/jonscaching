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
use Caching\BlogBundle\Entity\EntryImage;
use Caching\BlogBundle\Form\Type\EntryType;
use Caching\BlogBundle\Form\Type\UploadType;
use JonsCaching\GpxReader\GpxReader;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class HomeController extends Controller
{
    public function indexAction()
    {
        $form       = $this->createForm(new EntryType());
        $user       = $this->get('security.context')->getToken()->getUser();
        $em         = $this->getDoctrine()->getEntityManager();
        $limit      = $this->container->parameters['initial_limit'];
        $entries    = $em->getRepository('Caching\BlogBundle\Entity\Entry')->fetchInitial($limit);

        return $this->render('CachingBlogBundle:Home:index.html.twig', array(
            'user'      => $user,
            'form'      => $form->createView(),
            'entries'   => $entries,
            'limit'     => $limit,
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
                $entry->setActive(Entry::ACTIVE);
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

    public function buildingBlocksAction() {
        return $this->render('CachingBlogBundle:Home:building_blocks.html.twig');
    }

    public function uploadPhotoAction(Request $request)
    {
        $file       = $request->files->get('file');
        $entryid    = $request->request->get('entry_id');
        $imagine    = new Imagine();
        $em         = $this->getDoctrine()->getEntityManager();
        $entry      = $em->getRepository('CachingBlogBundle:Entry')
                        ->find($entryid);

        // Save image path and filename for later use
        $rootPath           = $this->container->parameters['upload_dir'] . $entry->getRoute()->getFolderName();
        $imageName          = $file->getClientOriginalName();
        $fullImagePath      = $rootPath . '/fullsize/' . $imageName;
        $relativeFullPath   = 'images/routes/' . $entry->getRoute()->getFolderName() . '/fullsize/';
        $thumbImagePath     = $rootPath . '/thumbs/' . $imageName;
        $relativeThumbPath  = 'images/routes/' . $entry->getRoute()->getFolderName() . '/thumbs/';
        $thumbWidth         = $this->container->parameters['thumb_width'];
        $fullsizeWidth      = $this->container->parameters['fullsize_width'];

        // Move a copy in the fullsize and thumbs directory
        $file->move($rootPath . '/fullsize/', $imageName);

        // Get the uploaded image width and height
        $origImageSize  = getimagesize($fullImagePath);

        // Create Imagine instance for full size image
        $fullsizeImage  = $imagine->open($fullImagePath);

        // Calculate the height for each image, keeping scale the same
        $newFullHeight  = $fullsizeWidth / ($origImageSize[0] / $origImageSize[1]);
        $newThumbHeight = $thumbWidth / ($origImageSize[0] / $origImageSize[1]);

        // Resize and save the fullsize image
        $fullsizeImage->resize(new Box($fullsizeWidth, $newFullHeight))
            ->save($fullImagePath);

        // If there is no directory for the thumb image, create one or an error will happen when
        // trying to copy
        if (!is_dir($relativeThumbPath)) {
            mkdir($relativeThumbPath, 0777, true);
        }

        // Make a thumb copy of the full image
        copy($fullImagePath, $thumbImagePath);

        // Resize and save the thumb image
        $thumbImage     = $imagine->open($thumbImagePath);
        $thumbImage->resize(new Box($thumbWidth, $newThumbHeight))
            ->save($thumbImagePath);

        $entryImage = new EntryImage();
        $entryImage->setEntry($entry);
        $entryImage->setFullPath($relativeFullPath . $imageName);
        $entryImage->setThumbPath($relativeThumbPath . $imageName);
        $em->persist($entryImage);

        $entry->addImage($entryImage);

        $em->flush();

        $json = array(
            'success'   => 1,
            'entry_id'  => $entryid,
        );

        // Return the json response
        return $this->render('CachingBlogBundle:Home:json.html.twig', array(
            'json' => json_encode($json),
        ));
    }

    public function loadArticleAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $em         = $this->getDoctrine()->getEntityManager();
            $offset     = $request->request->get('offset');

            try {
                $entry      = $em->getRepository('Caching\BlogBundle\Entity\Entry')->fetchNextPost($offset);
                $html = $this->renderView('CachingBlogBundle:Home:entry.html.twig', array(
                    'entry' => $entry,
                ));

                $json = array(
                    'success'       => 1,
                    'new_offset'    => $offset + 1,
                    'html'          => $html,
                );
            } catch (\Doctrine\ORM\NoResultException $e) {
                $json = array(
                    'success'       => 0,
                    'new_offset'    => 'done',
                );
            }

            // Return the json response
            return $this->render('CachingBlogBundle:Home:json.html.twig', array(
                'json' => json_encode($json),
            ));
        }
    }
}