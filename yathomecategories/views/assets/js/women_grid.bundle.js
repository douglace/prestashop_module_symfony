const { $ } = window; 
 
$(() => { 
  const grid = new window.prestashop.component.Grid('yhc_women'); 
 
  grid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.AsyncToggleColumnExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension()); 
  grid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension()); 
  grid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension()); 
  grid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension()); 
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension()); 
  grid.addExtension(new window.prestashop.component.GridExtensions.PositionExtension()); 
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
}); 