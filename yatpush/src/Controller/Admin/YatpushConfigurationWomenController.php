<?php 
namespace Yateo\Yatpush\Controller\Admin; 

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController; 
use PrestaShopBundle\Security\Annotation\AdminSecurity; 
use Yateo\Yatpush\Grid\Definition\Factory\WomenGridDefinitionFactory;
use Yateo\Yatpush\Grid\Filters\WomenFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yateo\Yatpush\Command\DeletePushCommand;
use Yateo\Yatpush\Command\TogglePushCommand;
use Yateo\Yatpush\Exception\CannotDeleteImageException;
use Yateo\Yatpush\Exception\CannotDeletePushException;
use Yateo\Yatpush\Exception\CannotTogglePushException;
use Yateo\Yatpush\Exception\CannotUpdatePositionException;
use Yateo\Yatpush\Query\GetPushIsEnabled;
use Yateo\Yatpush\Repository\PushRepository;
 
class YatpushConfigurationWomenController extends FrameworkBundleAdminController 
{ 

    use CommonPushTrait;
    
    /** 
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))") 
     * @return void 
     */ 
    public function indexAction(Request $request, WomenFilters $filters) 
    { 
        $womenGridFactory = $this->get('yatpush.grid.grid_factory.women');
        $womenGrid = $womenGridFactory->getGrid($filters);
        
        return $this->render(
            '@Modules/yatpush/views/templates/admin/women.html.twig',
            [
                'enableSidebar' => true,
                'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
                'layoutTitle' => $this->trans('Women push listing', 'Modules.Yatpush.Admin'),
                'womenGrid' => $this->presentGrid($womenGrid),
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
            $this->get('yatpush.grid.definition.factory.women'),
            $request,
            WomenGridDefinitionFactory::GRID_ID,
            'yatpush_pushwomen'
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
        $pushFormBuilder = $this->get('yatpush.form.identifiable.object.builder.push');
        $pushForm = $pushFormBuilder->getForm(['type' => "WOMAN"], []);
        $pushForm->handleRequest($request);
        
        $pushFormHandler = $this->get('yatpush.form.identifiable.object.handler.push');
        $result = $pushFormHandler->handle($pushForm);
       
        if (null !== $result->getIdentifiableObjectId()) {
            $this->addFlash(
                'success',
                $this->trans('Successful creation.', 'Admin.Notifications.Success')
            );

            return $this->redirectToRoute('yatpush_pushwomen');
        }

        return $this->render('@Modules/yatpush/views/templates/admin/create.html.twig', [
            'pushForm' => $pushForm->createView(),
        ]);
    }

    /**
     * Edit push
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($pushId, Request $request)
    {
        
        $pushFormBuilder = $this->get('yatpush.form.identifiable.object.builder.push');
        $pushForm = $pushFormBuilder->getFormFor($pushId, [
            'type' => "WOMAN",
            'pushId' => $pushId,
        ], ['allow_file_upload' => true]);
        $pushForm->handleRequest($request);

        $pushFormHandler = $this->get('yatpush.form.identifiable.object.handler.push');
        $result = $pushFormHandler->handleFor($pushId, $pushForm);
        
        if (null !== $result->getIdentifiableObjectId()) {
            $this->addFlash(
                'success',
                $this->trans('Successful edition.', 'Admin.Notifications.Success')
            );

            return $this->redirectToRoute('yatpush_pushwomen');
        }

        return $this->render('@Modules/yatpush/views/templates/admin/create.html.twig', [
            'pushForm' => $pushForm->createView(),
        ]);
    }

    /**
     * Edit push
     *
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction($pushId, Request $request)
    {
        
        $res = $this->getCommandBus()->handle(new DeletePushCommand((int) $pushId));

        if ($res == true)
        {
            $this->deleteUploadedImage($pushId);
            $this->addFlash(
                'success',
                $this->trans('Successful deletion.', 'Admin.Notifications.Success')
            );
        } else {
            $this->addFlash(
                'error',
                $this->trans('Something wen wrong.', 'Admin.Notifications.Success')
            );
        }
        
        return $this->redirectToRoute('yatpush_pushwomen');
        
    }

    public function deleteImageAction($pushId)
    {
        try {
            $this->deleteUploadedImage($pushId);

            $this->addFlash(
                'success',
                $this->trans('The image was successfully deleted.', 'Admin.Notifications.Success')
            );
        } catch (CannotDeleteImageException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('yatpush_pushwomen_edit', [
            'pushId' => $pushId
        ]);
    }

    /**
     * Toggle category status.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this."
     * )
     *
     * @param int $pushId
     *
     * @return JsonResponse
     */
    public function toggleAction($pushId)
    {

        try {
            $isEnabled = $this->getQueryBus()->handle(new GetPushIsEnabled((int) $pushId));

            $this->getCommandBus()->handle(
                new TogglePushCommand((int) $pushId, !$isEnabled)
            );

            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success'),
            ];
        } catch (CannotTogglePushException $e) {
            $response = [
                'status' => false,
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages()),
            ];
        }

        return $this->json($response);
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
        $positionsData = [
            'positions' => $request->request->get('positions', null),
        ];
        
        /** @var PushRepository $repository */
        $repository = $this->get('yatpush.repository.push.repository');

        try {
            $repository->updatePositions('WOMAN', $positionsData);
           
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (CannotUpdatePositionException $e) {
            $errors = [$e->getMessage()];
            $this->flashErrors($errors);
        }

        return $this->redirectToRoute('yatpush_pushwomen');
    }


    /**
     * Get translated error messages for category exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            CannotTogglePushException::class => $this->trans('Unable to toggle status.', 'Admin.Notifications.Error'),
            CannotDeletePushException::class => $this->trans('Unable delete image', 'Admin.Notifications.Error')
        ];
    }

    public function exportAction(WomenFilters $filters)
    {
        $filters = new WomenFilters(['limit' => null] + $filters->all());
        $pushGridFactory = $this->get('yatpush.grid.grid_factory.men');
        $pushGrid = $pushGridFactory->getGrid($filters);

        $headers = [
            'id_yateo_push' => $this->trans('ID', 'Admin.Global'),
            'id_lang' => $this->trans('Langue', 'Admin.Global'),
            'type' => $this->trans('Type', 'Admin.Global'),
            'link' => $this->trans('Lien', 'Admin.Global'),
            'text_top' => $this->trans('Top', 'Admin.Global'),
            'text_bottom' => $this->trans('Bottom', 'Admin.Global'),
            'text_button' => $this->trans('Button', 'Admin.Global'),
            'position' => $this->trans('Position', 'Admin.Global'),
            'active' => $this->trans('Displayed', 'Admin.Global'),
        ];

        $data = [];

        foreach ($pushGrid->getData()->getRecords()->all() as $record) {
            $data[] = [
                'id_yateo_push' => $record['id_yateo_push'],
                'id_lang' => $record['id_lang'],
                'type' => $record['type'],
                'text_top' => $record['text_top'],
                'text_bottom' => $record['text_bottom'],
                'text_button' => $record['text_button'],
                'position' => $record['position'],
                'active' => $record['active'],
            ];
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('push_men_' . date('Y-m-d_His') . '.csv');
    }



    /** 
     * @return array[] 
     */ 
    private function getToolbarButtons() 
    { 
        return [ 
            'add' => [ 
                'desc' => $this->trans('Add new push', 'Modules.Yatpush.Admin'), 
                'icon' => 'add_circle_outline',
                'href' => $this->generateUrl('yatpush_pushwomen_create'), 
            ], 
            'men' => [ 
                'desc' => $this->trans('Men list', 'Modules.Yatpush.Admin'), 
                'icon' => 'list',
                'href' => $this->generateUrl('yatpush_pushmen'), 
                'class' => 'btn-outline-primary',
            ],  
        ]; 
    } 
} 