<?php

namespace EasyAdminTranslationsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TranslationController extends Controller
{

    /**
     * Redirect to the proper translation entity edit form base on query params
     *
     * @Route("/eatb/redirect-to-translation/{entityName}/{parentId}/{languageId}", name="redirect-to-translation")
     * @Method("GET")
     * @return RedirectResponse
     */
    public function redirectToTranslationEditFormAction(Request $request, $entityName, $parentId, $languageId)
    {
        // Get full namespace of the entity
        $translationClassName = $this->get('easyadmin.config.manager')->getEntityConfig($entityName)['properties']['translations']['targetEntity'];

        // Get the translation of the entity in the corresponding language
        $translation = $this->getDoctrine()->getManager()->getRepository($translationClassName)->findOneBy([
            'parent'     => $parentId,
            'language'   => $languageId
        ]);

        // Not found ?
        if(is_null($translation)) {
            throw new NotFoundHttpException();
        }

        // Redirect to edit form for this translation
        $editFormUrlParams = [
            'action'    => 'edit',
            'entity'    => join('', array_slice(explode('\\', $translationClassName), -1)),
            'id'        => $translation->getId(),
            'hideMenu'  => 'true'
        ];
        $urlToEditForm = $this->generateUrl('easyadmin', $editFormUrlParams);
        return $this->redirectToRoute('easyadmin', array_merge($editFormUrlParams, [
            'sendToParent' => json_encode([
                'action'     => 'new',
                'language'   => 'fr_FR',
                'action_url' => $urlToEditForm
            ])
        ]));
    }

}
