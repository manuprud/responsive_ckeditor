<?php

namespace ActuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ActuBundle\Form\ActuType;
use ActuBundle\Entity\Actu;

class AdminActuController extends Controller {

    public function editionAction(Request $request) {

        $newActu = new Actu();
        $newform = $this->get('form.factory')->create(new ActuType, $newActu);
        $newform->handleRequest($request);
        if ($newform->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //établi relation manytomany actu::photo
            $listeidphoto = $newActu->getlisteimg();
            if (!empty($listeidphoto)) {
                //
                //********************* entité de photo ********************
                //*****lier les illustrations à l'article par leur id ******
                //
                $rp = $this->getDoctrine()->getManager()->getRepository("***********");
                foreach ($listeidphoto as $key => $value) {
                    $photo = $rp->findbyslug($value);
                    $newActu->addPhoto($photo);
                }
            }
            $em->persist($newActu);
            $em->flush();
            $request->getSession()->getFlashBag()->add('alertNewActu', 'nouvelle article ' . $newActu->getTitre() . ' créé');
            return $this->modificationAction();
        }
        return $this->render('$$$$$$$$$$$$', array('newformactu' => $newform->createview()));
    }

}
