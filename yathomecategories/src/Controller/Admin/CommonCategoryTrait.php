<?php

declare(strict_types=1);

namespace Yateo\Yathomecategories\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Yateo\Yathomecategories\Command\BulkEnableCategoryCommand;
use Yateo\Yathomecategories\Command\BulkDisableCategoryCommand;
use Yateo\Yathomecategories\Command\BulkDeleteCategoryCommand;
use Yateo\Yathomecategories\Command\DeleteCategoryCommand;
use Yateo\Yathomecategories\Command\ToggleCategoryCommand;
use Yateo\Yathomecategories\Uploader\YatCategoryImageUploader;
use Yateo\Yathomecategories\Exception\CannotDeleteImageException;
use Yateo\Yathomecategories\Exception\CannotDeleteCategoryException;
use Yateo\Yathomecategories\Exception\CannotEnableCategoryException;
use Yateo\Yathomecategories\Exception\CannotDisableCategoryException;
use Yateo\Yathomecategories\Exception\CannotToggleCategoryException;
use Yateo\Yathomecategories\Query\GetItemIsEnabled;
use Yateo\Yathomecategories\Exception\CannotUpdatePositionException;

trait CommonCategoryTrait
{

    /**
     * Get translated error messages for category exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            CannotToggleCategoryException::class => $this->trans('Unable to toggle status.', 'Admin.Notifications.Error'),
            CannotDeleteCategoryException::class => $this->trans('Unable delete image', 'Admin.Notifications.Error')
        ];
    }

    /**
     * delete multiple push.
     *
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulkAction(Request $request)
    {
        $men = true;
        $categoriesToDelete = $request->request->get('yhc_men_id_yateo_home_categories');
        
        if(empty($categoriesToDelete)) {
            $men = false; 
            $categoriesToDelete = $request->request->get('yhc_women_id_yateo_home_categories');
        }
        
        try {
            if(!empty($categoriesToDelete)) {
                $categoriesToDelete = array_map(function ($item) { return (int) $item; }, $categoriesToDelete);

                $this->getCommandBus()->handle(
                    new BulkDeleteCategoryCommand($categoriesToDelete)
                );

                foreach($categoriesToDelete as $id)
                {
                    $this->deleteUploadedImage($id);
                }
                
                $this->addFlash(
                    'success',
                    $this->trans('The items has been successfully delete.', 'Admin.Notifications.Success')
                );
            }
            
        } catch (CannotDeleteCategoryException $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        if($men)
            return $this->redirectToRoute('yhc_men');
        else
            return $this->redirectToRoute('yhc_women');
    }

    /**
     * Changes multiple  push statuses to enabled.
     *
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkStatusEnableAction(Request $request)
    {
        $men = true;
        $categoriesToEnable = $request->request->get('yhc_men_id_yateo_home_categories');
        
        if(empty($categoriesToEnable)) {
            $men = false; 
            $categoriesToEnable = $request->request->get('yhc_women_id_yateo_home_categories');
        }
        
        try {
            $categoriesToEnable = array_map(function ($item) { return (int) $item; }, $categoriesToEnable);
            
            $this->getCommandBus()->handle(
                new BulkEnableCategoryCommand($categoriesToEnable)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CannotEnableCategoryException $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        if($men)
            return $this->redirectToRoute('yhc_men');
        else
            return $this->redirectToRoute('yhc_women');
    }

    /**
     * Changes multiple push statuses to disable.
     *
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkStatusDisableAction(Request $request)
    {
        $men = true;
        $categoriesToDisable = $request->request->get('yhc_men_id_yateo_home_categories');
        
        if(empty($categoriesToDisable)) {
            $men = false; 
            $categoriesToDisable = $request->request->get('yhc_women_id_yateo_home_categories');
        }
        
        try {
            $categoriesToDisable = array_map(function ($item) { return (int) $item; }, $categoriesToDisable);

            $this->getCommandBus()->handle(
                new BulkDisableCategoryCommand($categoriesToDisable)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (CannotDisableCategoryException $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        if($men)
            return $this->redirectToRoute('yhc_men');
        else
            return $this->redirectToRoute('yhc_women');
    }

    /**
     * @param int $supplierId
     *
     * @return bool
     * @throws CannotDeleteImageException
     */
    private function deleteUploadedImage($itemId)
    {
        $path = $this->findFileByPrefix(
            _PS_MODULE_DIR_ .YatCategoryImageUploader::IMAGE_PATH,
            $itemId.'_'
        );
        
        if (($path && !file_exists($path)) || $path === null) {
            return true;
        }
       
        if (is_string($path) && @unlink($path)) {
            return true;
        }

        throw new YatCategoryImageUploader(sprintf(
            'Cannot delete image with id "%s"',
            $path
        ));
    }

    private function findFileByPrefix($directory, $prefix) {
        $files = scandir($directory);
    
        foreach($files as $file) {
            if (substr($file, 0, strlen($prefix)) === $prefix) {
                return rtrim($directory, '/') . '/' . $file;
            }
        }
    
        return null; // Aucun fichier correspondant trouvé
    }

    /**
     * Toggle category status.
     *
     * @AdminSecurity(
     *     "is_granted(['update'], request.get('_legacy_controller'))",
     *     message="You do not have permission to update this."
     * )
     *
     * @param int $itemId
     *
     * @return JsonResponse
     */
    public function toggleAction($itemId)
    {

        try {
            $isEnabled = $this->getQueryBus()->handle(new GetItemIsEnabled((int) $itemId));

            $this->getCommandBus()->handle(
                new ToggleCategoryCommand((int) $itemId, !$isEnabled)
            );

            $response = [
                'status' => true,
                'message' => $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success'),
            ];
        } catch (CannotToggleCategoryException $e) {
            $response = [
                'status' => false,
                'message' => $this->getErrorMessageForException($e, $this->getErrorMessages()),
            ];
        }

        return $this->json($response);
    }

    private function deleteItem(int $itemId)
    {
        $res = $this->getCommandBus()->handle(new DeleteCategoryCommand((int) $itemId));

        if ($res == true)
        {
            $this->deleteUploadedImage($itemId);
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
    }

    private function updatePosition(Request $request, $type = "MAN")
    {
        $positionsData = [
            'positions' => $request->request->get('positions', null),
        ];
        
        /** @var Yateo\Yathomecategories\Repository\CategoryRepository $repository */
        $repository = $this->get('yhc.repository.category.repository');

        try {
            $repository->updatePositions($type, $positionsData);
           
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (CannotUpdatePositionException $e) {
            $errors = [$e->getMessage()];
            $this->flashErrors($errors);
        }
    }
}