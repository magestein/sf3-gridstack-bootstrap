<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Grid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    /**
     * @Route("/save", name="save")
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $serialized = $request->request->get('serialized');
            $sessionId = $request->request->get('sessionId');
            $em = $this->getDoctrine()->getManager();

            $grid = $em->getRepository('AppBundle:Grid')->findOneBySessionId($sessionId);

            if(null === $grid) {
                $grid = new Grid();
                $grid->setSessionId($sessionId);
                $grid->setSerialized($serialized);
            } else {
                $grid->setSerialized($serialized);
            }

            $em->persist($grid);
            $em->flush();

            return new JsonResponse(array('success' => true));
        }
    }

    /**
     * @Route("/{sessionId}", name="homepage")
     * @param Request $request
     * @param null $sessionId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $sessionId = null)
    {
        // Start session
        $session = $request->getSession();
        if(!$session->isStarted()) {
            $session->start();
        }

        // If no sessionId parameter specified, get it from current session
        if(null === $sessionId) {
            $sessionId = $session->getId();
        }

        // Load grid from database
        $em = $this->getDoctrine()->getManager();
        $grid = $em->getRepository('AppBundle:Grid')->findOneBySessionId($sessionId);

        if(null != $grid) {
            $session->getFlashBag()->add('info',
                'Grid <strong>' . $sessionId . '</strong> loaded from <strong>database</strong>.');
            $grid_json = json_decode($grid->getSerialized());

            // Parse JSON and put box id as key
            $grid = array();
            foreach($grid_json as $box) {
                $id = $box->id;
                $grid[$id] = $box;
            }
        } else {
            $session->getFlashBag()->add('warning', 'Default grid loaded. Drag any box to <strong>save the grid!</strong>');
            $grid = $this->getDefaultGrid();
        }

        // Load contetn for each box
        $grid = $this->loadContent($grid);

        return $this->render('default/index.html.twig', array(
            'session_id' => $sessionId,
            'grid' => $grid
        ));
    }

    private function getDefaultGrid()
    {
        return array(
            "box1" => array(
                "id" => "box1",
                "x" => 0,
                "y" => 0,
                "width" => 6,
                "height" => 6
            ),
            "box2" => array(
                "id" => "box2",
                "x" => 6,
                "y" => 0,
                "width" => 6,
                "height" => 3
            ),
            "box3" => array(
                "id" => "box3",
                "x" => 6,
                "y" => 3,
                "width" => 3,
                "height" => 3
            ),
            "box4" => array(
                "id" => "box4",
                "x" => 9,
                "y" => 3,
                "width" => 3,
                "height" => 3
            ),
        );
    }

    private function loadContent($grid)
    {
        $content = array(
            array(
                'title' => 'In sit amet quam eu odio gravida fringilla',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sed purus mollis nibh placerat dignissim nec eget urna. Integer posuere scelerisque ipsum sit amet semper. In eu convallis felis. In eget rhoncus mauris. Sed quis ligula non nulla cursus sagittis fringilla at libero. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin scelerisque erat et consectetur pellentesque.'
            ),
            array(
                'title' => 'Phasellus mattis velit non aliquam iaculis',
                'content' => ' Praesent vitae tincidunt elit, sit amet finibus lacus. Donec fermentum, ipsum id ullamcorper bibendum, tortor libero cursus orci, a vestibulum augue metus ac eros. Suspendisse semper ante sit amet tellus interdum ultrices. Nullam ut nisi sodales est cursus finibus a vitae magna. Donec gravida purus quis risus pellentesque, et tristique ligula pellentesque. Interdum et malesuada fames ac ante ipsum primis in faucibus. Sed tempor nisl vel turpis gravida aliquam. Vivamus pretium id velit vel gravida. Suspendisse iaculis nunc ac metus viverra luctus. Suspendisse et sapien id nisi ultricies cursus sit amet sit amet odio. Nunc eget quam erat. Vivamus sapien magna, commodo ut mattis vitae, finibus vel purus.'
            ),
            array(
                'title' => 'Aenean vel arcu sit amet leo luctus viverra',
                'content' => 'Quisque at odio sed velit efficitur luctus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Curabitur faucibus ipsum vitae nisl scelerisque laoreet. Donec magna lorem, fermentum ut dui tristique, congue cursus est. Vivamus eget dapibus mi, vitae commodo arcu. Quisque felis massa, dapibus quis nunc ut, porta efficitur magna. Fusce id sapien in libero imperdiet pellentesque. Aliquam erat ligula, cursus in scelerisque ac, porta vel dolor. Proin quis feugiat elit. Integer porttitor ligula commodo lectus mattis, non ultrices ante luctus. Cras cursus sapien nulla, a venenatis dolor pulvinar eget. Maecenas pellentesque elit ac neque hendrerit, et dictum turpis malesuada. Aliquam vitae urna risus. Quisque nec neque aliquam, scelerisque est vitae, dignissim nisi. Cras vitae convallis enim.'
            ),
            array(
                'title' => 'Ut interdum magna eget lacinia tincidunt',
                'content' => 'Aenean nec orci consectetur justo lobortis finibus sed eu dolor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aliquam vel nisi sodales, suscipit enim et, volutpat ante. Curabitur magna erat, tincidunt id sollicitudin et, maximus nec justo. Etiam sed lectus id ante vehicula consectetur sed nec urna. Nullam consectetur sit amet metus tempor aliquet. Suspendisse vel rutrum velit.'
            ),
            array(
                'title' => 'Sed placerat eros consequat arcu ornare vehicula',
                'content' => 'Morbi blandit, dolor in efficitur ullamcorper, felis odio tempor ipsum, vitae placerat lectus est quis magna. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Morbi eros orci, iaculis id augue fringilla, viverra luctus diam. Maecenas nisi velit, dignissim ac facilisis sit amet, egestas et neque. Aenean odio turpis, porttitor ut dictum nec, viverra eu turpis. Aliquam consectetur nulla ligula, non volutpat eros sollicitudin in. Praesent mollis augue sagittis dui lacinia lacinia. Duis vehicula egestas eros, ut mollis sem condimentum et'
            ),
        );

        foreach($grid as $id => $box) {
            // You can include here a switch($id)
            if(is_array($grid[$id])) {
                $grid[$id]['content']['title'] = $content;
            } else {
                $grid[$id]->content = $content;
            }

        }

        return $grid;
    }
}
