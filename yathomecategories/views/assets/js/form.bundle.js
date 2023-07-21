const { $ } = window; 
 
$(() => { 
  window.prestashop.component.initComponents([
    'TranslatableField',
    'TranslatableInput',
  ]); 
  
}); 