<?php

declare(strict_types=1);

namespace Yateo\Yatpush\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Yateo\Yatpush\Command\BulkEnablePushCommand;
use Yateo\Yatpush\Command\BulkDisablePushCommand;
use Yateo\Yatpush\Command\BulkDeletePushCommand;
use Yateo\Yatpush\Uploader\YatpushImageUploader;
use Yateo\Yatpush\Exception\CannotDeleteImageException;
use Yateo\Yatpush\Exception\PushException;

trait CommonPushTrait
{

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
        $pushsToDelete = $request->request->get('push_men_id_yateo_push');
        
        if(empty($pushsToDelete)) {
            $men = false; 
            $pushsToDelete = $request->request->get('push_women_id_yateo_push');
        }
        
        try {
            if(!empty($pushsToDelete)) {
                $pushsToDelete = array_map(function ($item) { return (int) $item; }, $pushsToDelete);

                $this->getCommandBus()->handle(
                    new BulkDeletePushCommand($pushsToDelete)
                );

                foreach($pushsToDelete as $id)
                {
                    $this->deleteUploadedImage($id);
                }
                
                $this->addFlash(
                    'success',
                    $this->trans('The items has been successfully delete.', 'Admin.Notifications.Success')
                );
            }
            
        } catch (PushException $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        if($men)
            return $this->redirectToRoute('yatpush_pushmen');
        else
            return $this->redirectToRoute('yatpush_pushwomen');
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
        $pushsToEnable = $request->request->get('push_men_id_yateo_push');
        
        if(empty($pushsToEnable)) {
            $men = false; 
            $pushsToEnable = $request->request->get('push_women_id_yateo_push');
        }
        
        try {
            $pushsToEnable = array_map(function ($item) { return (int) $item; }, $pushsToEnable);

            $this->getCommandBus()->handle(
                new BulkEnablePushCommand($pushsToEnable)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (PushException $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        if($men)
            return $this->redirectToRoute('yatpush_pushmen');
        else
            return $this->redirectToRoute('yatpush_pushwomen');
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
        $pushsToDisable = $request->request->get('push_men_id_yateo_push');
        
        if(empty($pushsToDisable)) {
            $men = false; 
            $pushsToDisable = $request->request->get('push_women_id_yateo_push');
        }
        
        try {
            $pushsToDisable = array_map(function ($item) { return (int) $item; }, $pushsToDisable);

            $this->getCommandBus()->handle(
                new BulkDisablePushCommand($pushsToDisable)
            );

            $this->addFlash(
                'success',
                $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
            );
        } catch (PushException $exception) {
            $this->addFlash(
                'error',
                $this->getErrorMessageForException($exception, $this->getErrorMessages())
            );
        }

        if($men)
            return $this->redirectToRoute('yatpush_pushmen');
        else
            return $this->redirectToRoute('yatpush_pushwomen');
    }

    /**
     * @param int $supplierId
     *
     * @return bool
     * @throws CannotDeleteImageException
     */
    private function deleteUploadedImage($pushId)
    {
        $imgPath = _PS_MODULE_DIR_ .YatpushImageUploader::IMAGE_PATH. $pushId . '.jpg';

        if (!file_exists($imgPath)) {
            return true;
        }

        if (unlink($imgPath)) {
            return true;
        }

        throw new CannotDeleteImageException(sprintf(
            'Cannot delete image with id "%s"',
            $pushId . '.jpg'
        ));
    }

    
}