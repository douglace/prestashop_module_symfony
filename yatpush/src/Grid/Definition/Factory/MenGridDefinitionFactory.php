<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);

namespace Yateo\Yatpush\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\IdentifierColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;

use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection; 
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction; 
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction; 
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn; 
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\BulkDeleteActionTrait;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\DeleteActionTrait;


class MenGridDefinitionFactory extends AbstractGridDefinitionFactory
{

    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    const GRID_ID = 'push_men';

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

     /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Men', [], 'Modules.Yatpush.Admin');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new IdentifierColumn('id_yateo_push'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'identifier_field' => 'id_yateo_push',
                    'bulk_field' => 'id_yateo_push',
                    'with_bulk_field' => true,
                    'clickable' => false,
                ])
            )
            ->add((new ImageColumn('logo'))
                ->setName($this->trans('Logo', [], 'Admin.Global'))
                ->setOptions([
                    'src_field' => 'logo',
                ])
            )
            ->add(
                (new DataColumn('text_top'))
                    ->setName($this->trans('Titre top', [], 'Modules.Yatpush.Admin'))
                    ->setOptions([
                        'field' => 'text_top',
                ])
            )
            ->add(
                (new DataColumn('text_bottom'))
                    ->setName($this->trans('Titre botom', [], 'Modules.Yatpush.Admin'))
                    ->setOptions([
                        'field' => 'text_bottom',
                ])
            )
            ->add(
                (new PositionColumn('position'))
                ->setName($this->trans('Position', [], 'Admin.Global'))
                ->setOptions([
                    'id_field' => 'id_yateo_push',
                    'position_field' => 'position',
                    'update_route' => 'yatpush_pushmen_update_positions',
                    'update_method' => 'POST',
                    'record_route_params' => [
                        'id_yateo_push' => 'pushId',
                    ],
                ])
            )
            ->add( 
                (new ToggleColumn('active')) 
                    ->setName($this->trans('Displayed', [], 'Admin.Global')) 
                    ->setOptions([ 
                        'field' => 'active', 
                        'primary_field' => 'id_yateo_push', 
                        'route' => 'yatpush_pushmen_toggle_status', 
                        'route_param_name' => 'pushId', 
                    ]) 
            ) 
            ->add( 
                (new ActionColumn('actions')) 
                ->setName($this->trans('Actions', [], 'Admin.Global')) 
                ->setOptions([ 
                    'actions' => (new RowActionCollection()) 
                    ->add( 
                        (new LinkRowAction('edit')) 
                        ->setName($this->trans('Edit', [], 'Admin.Actions')) 
                        ->setIcon('edit') 
                        ->setOptions([ 
                            'route' => 'yatpush_pushmen_edit', 
                            'route_param_name' => 'pushId', 
                            'route_param_field' => 'id_yateo_push', 
                            'clickable_row' => true, 
                        ]) 
                    ) 
                    ->add( 
                        (new SubmitRowAction('delete')) 
                        ->setName($this->trans('Delete', [], 'Admin.Actions')) 
                        ->setIcon('delete') 
                        ->setOptions([ 
                            'route' => 'yatpush_pushmen_delete', 
                            'route_param_name' => 'pushId', 
                            'route_param_field' => 'id_yateo_push', 
                            'confirm_message' => $this->trans( 
                                'Delete selected item?', 
                                [], 
                                'Admin.Notifications.Warning' 
                            ), 
                        ]) 
                    ) 
                ]) 
            ) 
        ; 
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_yateo_push', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('ID', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('id_yateo_push')
            )->add(
                (new Filter('text_top', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Titre top', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('text_top')
            )->add(
                (new Filter('text_bottom', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Titre bas', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('text_bottom')
            )->add(
                (new Filter('position', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Position', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('position')
            )->add(
                (new Filter('active', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('active')
            )->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'yatpush_pushmen',
                    ])
                    ->setAssociatedColumn('actions')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new LinkGridAction('export'))
                    ->setName($this->trans('Export', [], 'Admin.Actions'))
                    ->setIcon('cloud_download')
                    ->setOptions([
                        'route' => 'yatpush_pushmen_export',
                    ])
            )
            ->add((new SimpleGridAction('common_refresh_list'))
                ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                ->setIcon('refresh')
            )
            ->add((new SimpleGridAction('common_show_query'))
                ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                ->setIcon('code')
            )
            ->add((new SimpleGridAction('common_export_sql_manager'))
                ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                ->setIcon('storage')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('enable_selection'))
                ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'yatpush_pushmen_bulk_status_enable',
                ])
            )
            ->add((new SubmitBulkAction('disable_selection'))
                ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'yatpush_pushmen_bulk_status_disable',
                ])
            )
            ->add(
                $this->buildBulkDeleteAction('yatpush_pushmen_delete_bulk')
            )
        ;
    }

}