<?php

namespace Kolyya\OAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ConnectController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function disconnectAjaxAction(Request $request)
    {
        $id = $request->get('id',null);

        $user = $this->getUser();

        if('vk' == $id){
            $user->setVkontakteId(null);
            $user->setVkontakteData(null);
        }

        if('fb' == $id){
            $user->setFacebookId(null);
            $user->setFacebookData(null);
        }

        if('ok' == $id){
            $user->setOdnoklassnikiId(null);
            $user->setOdnoklassnikiData(null);
        }

        if('mr' == $id){
            $user->setMailruId(null);
            $user->setMailruData(null);
        }

        if('gg' == $id){
            $user->setGoogleId(null);
            $user->setGoogleData(null);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse(array(
            'success' => true
        ));
    }
}