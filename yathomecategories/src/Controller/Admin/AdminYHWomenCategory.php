<?php

declare(strict_types=1);

namespace Yateo\Yathomecategories\Controller\Admin;


use Yateo\Yathomecategories\Grid\Definition\Factory\WomenGridDefinitionFactory;
use PrestaShopBundle\Component\CsvResponse;
use Yateo\Yathomecategories\Grid\Filters\WomenCategoryFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController; 
use PrestaShopBundle\Security\Annotation\AdminSecurity; 
use Symfony\Component\HttpFoundation\Request;
use Yateo\Yathomecategories\Command\DeleteCategoryCommand;
use Yateo\Yathomecategories\Exception\CannotDeleteImageException;

class AdminYHWomenCategory extends FrameworkBundleAdminController
{
    use CommonCategoryTrait;

    /** 
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))") 
     * @return void 
     */ 
    public function indexAction(Request $request, WomenCategoryFilters $filters)
    {
        $womenGridFactory = $this->get('yhc.grid.grid_factory.women');
        $womenGrid = $womenGridFactory->getGrid($filters);
        
        $womenConfigFormDataHandler = $this->get('yhc.form.category_women_configuration_data_handler');
        $womenConfigForm = $womenConfigFormDataHandler->getForm();
        $womenConfigForm->handleRequest($request);

        if ($womenConfigForm->isSubmitted() && $womenConfigForm->isValid()) {
            /** You can return array of errors in form handler and they can be displayed to user with flashErrors */
            $errors = $womenConfigFormDataHandler->save($womenConfigForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('yhc_women');
            }

            $this->flashErrors($errors);
        }

        return $this->render(
            '@Modules/yathomecategories/views/templates/admin/women.html.twig',
            [
                'enableSidebar' => true,
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
                'layoutTitle' => $this->trans('Categories femme', 'Modules.Yathomecategories.Admin'),
                'womenGrid' => $this->presentGrid($womenGrid),
                'configForm' => $womenConfigForm->createView(),
            ]
        );  
    }


    /**
     * Provides filters functionality.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('yhc.grid.definition.factory.men'),
            $request,
            WomenGridDefinitionFactory::GRID_ID,
            'yhc_men'
        );
    }

    /**
     * Create push
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $categoryFormBuilder = $this->get('yhc.form.identifiable.object.builder.category');
        $categoryForm = $categoryFormBuilder->getForm(['type' => "WOMAN"], []);
        $categoryForm->handleRequest($request);
        
        $categoryFormHandler = $this->get('yhc.form.identifiable.object.handler.category');
        $result = $categoryFormHandler->handle($categoryForm);
       
        if (null !== $result->getIdentifiableObjectId()) {
            $this->addFlash(
                'success',
                $this->trans('Successful creation.', 'Admin.Notifications.Success')
            );

            return $this->redirectToRoute('yhc_women');
        }

        return $this->render('@Modules/yathomecategories/views/templates/admin/create.html.twig', [
            'categoryForm' => $categoryForm->createView(),
        ]);
    }

    /**
     * Edit item
     *
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction($itemId, Request $request)
    {
        $this->deleteItem((int)$itemId);
        return $this->redirectToRoute('yhc_women');
    }

    /**
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="Access denied.")
     *
     * @param Request $request
     * @param int $hookId
     *
     * @throws \Exception
     *
     * @return RedirectResponse
     */
    public function updatePositionsAction(Request $request)
    {
        $this->updatePosition($request, 'WOMAN');
        return $this->redirectToRoute('yhc_women');
    }

    public function exportAction(WomenCategoryFilters $filters)
    {
        $filters = new WomenCategoryFilters(['limit' => null] + $filters->all());
        $pushGridFactory = $this->get('yhc.grid.grid_factory.women');
        $pushGrid = $pushGridFactory->getGrid($filters);

        $headers = [
            'id_yateo_home_categories' => $this->trans('ID', 'Admin.Global'),
            'id_lang' => $this->trans('Langue', 'Admin.Global'),
            'type' => $this->trans('Type', 'Admin.Global'),
            'link' => $this->trans('Lien', 'Admin.Global'),
            'title' => $this->trans('Titre', 'Admin.Global'),
            'position' => $this->trans('Position', 'Admin.Global'),
            'active' => $this->trans('Displayed', 'Admin.Global'),
        ];

        $data = [];

        foreach ($pushGrid->getData()->getRecords()->all() as $record) {
            $data[] = [
                'id_yateo_home_categories' => $record['id_yateo_home_categories'],
                'id_lang' => $record['id_lang'],
                'type' => $record['type'],
                'title' => $record['title'],
                'position' => $record['position'],
                'active' => $record['active'],
            ];
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('category_women_' . date('Y-m-d_His') . '.csv');
    }

    /**
     * Edit push
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($itemId, Request $request)
    {
        
        $categoryFormBuilder = $this->get('yhc.form.identifiable.object.builder.category');
        $categoryForm = $categoryFormBuilder->getFormFor($itemId, [
            'type' => "WOMAN",
            'itemId' => $itemId,
        ], ['allow_file_upload' => true]);
        $categoryForm->handleRequest($request);

        $categoryFormHandler = $this->get('yhc.form.identifiable.object.handler.category');
        $result = $categoryFormHandler->handleFor($itemId, $categoryForm);
        
        if (null !== $result->getIdentifiableObjectId()) {
            $this->addFlash(
                'success',
                $this->trans('Successful edition.', 'Admin.Notifications.Success')
            );

            return $this->redirectToRoute('yhc_women');
        }

        return $this->render('@Modules/yathomecategories/views/templates/admin/create.html.twig', [
            'categoryForm' => $categoryForm->createView(),
        ]);
    }


    public function deleteImageAction($itemId)
    {
        try {
            $this->deleteUploadedImage($itemId);

            $this->addFlash(
                'success',
                $this->trans('The image was successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (CannotDeleteImageException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('yhc_women_edit', [
            'itemId' => $itemId
        ]);
    }

    /** 
     * @return array[] 
     */ 
    private function getToolbarButtons() 
    { 
        return [ 
            'add' => [ 
                'desc' => $this->trans('Add new category', 'Modules.Yathomecategories.Admin'), 
                'icon' => 'add_circle_outline',
                'href' => $this->generateUrl('yhc_women_create'), 
            ], 
            'women' => [ 
                'desc' => $this->trans('Men category', 'Modules.Yathomecategories.Admin'), 
                'icon' => 'list',
                'href' => $this->generateUrl('yhc_men'), 
                'class' => 'btn-outline-primary',
            ], 
        ]; 
    } 
}