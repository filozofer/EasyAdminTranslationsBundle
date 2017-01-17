<?php

namespace EasyAdminTranslationsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TranslationController extends Controller
{

    /**
     * Redirect to the proper translation entity edit form base on query params
     *
     * @Route("/eatb/redirect-to-translation/{entityName}/{parentId}/{languageId}/{action}", name="redirect-to-translation")
     * @Method("GET")
     * @return RedirectResponse
     */
    public function redirectToTranslationEditFormAction(Request $request, $entityName, $parentId, $languageId, $action)
    {
        // Get full namespace of the entity
        $translationClassName = $this->get('easyadmin.config.manager')->getEntityConfig($entityName)['class'];

        // Get the translation of the entity in the corresponding language
        $translation = $this->getDoctrine()->getManager()->getRepository($translationClassName)->findOneBy([
            'parent'     => $parentId,
            'language'   => $languageId
        ]);

        // Get language entity
        $language = $this->getDoctrine()->getManager()->getRepository('EasyAdminTranslationsBundle:Language')->findOneById($languageId);

        // Not found ?
        if(is_null($translation) || is_null($language)) {
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
                'action'     => $action,
                'language'   => $language->getIsoKey(),
                'action_url' => $urlToEditForm
            ])
        ]));
    }

    /**
     * Empty page to just display flash message and before closing the tab
     *
     * @Route("/eatb/close-tab/{entityName}/{parentId}/{languageId}", name="eatb-close-tab")
     * @Method("GET")
     * @return RedirectResponse
     */
    public function closeTabAction(Request $request, $entityName, $parentId, $languageId)
    {
        // Get language entity
        $language = $this->getDoctrine()->getManager()->getRepository('EasyAdminTranslationsBundle:Language')->findOneById($languageId);

        // Not found ?
        if(is_null($language)) {
            throw new NotFoundHttpException();
        }

        // Generate path to new action for this Translation
        $refererToFutureEditPage = $this->generateUrl('redirect-to-translation', [
            'entityName'    => $entityName,
            'parentId'      => $parentId,
            'languageId'    => $languageId,
            'action'        => 'new'
        ]);
        $urlToCreateForm = $this->generateUrl('easyadmin', [
            'action'        => 'new',
            'entity'        => $entityName,
            'hideMenu'      => 'true',
            'language_id'   => $languageId,
            'parent_id'     => $parentId,
            'referer'       => $refererToFutureEditPage
        ]);

        // Start session to allow flash message print
        $this->get('session')->start();

        // Print view which display flash message and ask parent to close tab
        return $this->render('EasyAdminTranslationsBundle::close-tab.html.twig', [
            'language'   => $language->getIsoKey(),
            'action_url' => $urlToCreateForm
        ]);
    }

}
