<?php

require_once __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('UTC');

// START - PHPUNIT & COVERAGE SETUP
if (file_exists(__DIR__ . '/coverage.php')) {
include_once __DIR__ . '/coverage.php';
}

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/_includes/somedatadef.php';

$app = new \atk4\ui\App([
'call_exit' => (bool) ($_GET['APP_CALL_EXIT'] ?? true),
'catch_exceptions' => (bool) ($_GET['APP_CATCH_EXCEPTIONS'] ?? true),
]);

if ($app->call_exit !== true) {
$app->stickyGet('APP_CALL_EXIT');
}

if ($app->catch_exceptions !== true) {
$app->stickyGet('APP_CATCH_EXCEPTIONS');
}

if (file_exists(__DIR__ . '/coverage.php')) {
$app->onHook('beforeExit', function () {
    coverage();
});
}
// END - PHPUNIT & COVERAGE SETUP

$app->db = $db;
$app->title = 'Agile UI Demo v' . $app->version;

//[$rootUrl, $relUrl] = preg_split('~(?<=/)(?=demos(/|\?|$))|\?~s', $_SERVER['REQUEST_URI'], 3);
$demosUrl = $rootUrl . 'demos/';

if (file_exists(__DIR__ . '/../public/atkjs-ui.min.js')) {
$app->cdn['atk'] = $rootUrl . 'public';
}

$app->initLayout($app->stickyGET('layout') ?: \atk4\ui\Layout\Maestro::class);

$layout = $app->layout;
// Need for phpunit only for producing right url.
$layout->name = 'atk_admin';
$layout->id = $layout->name;

if ($layout instanceof \atk4\ui\Layout\Navigable) {
$layout->addMenuItem(['Welcome to Agile Toolkit', 'icon' => 'gift'], [$demosUrl . 'index']);

$path = $demosUrl . 'layout/';
$ly = $layout->addMenuGroup(['Layout', 'icon' => 'object group']);
$layout->addMenuItem(['Layouts'], [$path . 'layouts'], $ly);
$layout->addMenuItem(['Panel'], [$path . 'layout-panel'], $ly);

$path = $demosUrl . 'basic/';
$basic = $layout->addMenuGroup(['Basics', 'icon' => 'cubes']);
$layout->addMenuItem('View', [$path . 'view'], $basic);
$layout->addMenuItem('Button', [$path . 'button'], $basic);
$layout->addMenuItem('Header', [$path . 'header'], $basic);
$layout->addMenuItem('Message', [$path . 'message'], $basic);
$layout->addMenuItem('Labels', [$path . 'label'], $basic);
$layout->addMenuItem('Menu', [$path . 'menu'], $basic);
$layout->addMenuItem('BreadCrumb', [$path . 'breadcrumb'], $basic);
$layout->addMenuItem(['Columns'], [$path . 'columns'], $basic);
$layout->addMenuItem(['Grid Layout'], [$path . 'grid-layout'], $basic);

$path = $demosUrl . 'form/';
$form = $layout->addMenuGroup(['Form', 'icon' => 'edit']);
$layout->addMenuItem('Basics and Layouting', [$path . 'form'], $form);
$layout->addMenuItem('Data Integration', [$path . 'form2'], $form);
$layout->addMenuItem(['Form Sections'], [$path . 'form-section'], $form);
$layout->addMenuItem('Form Multi-column layout', [$path . 'form3'], $form);
$layout->addMenuItem(['Integration with Columns'], [$path . 'form5'], $form);
$layout->addMenuItem(['Custom Layout'], [$path . 'form-custom-layout'], $form);
$layout->addMenuItem(['Conditional Fields'], [$path . 'jscondform'], $form);

$path = $demosUrl . 'input/';
$in = $layout->addMenuGroup(['Input', 'icon' => 'keyboard outline']);
$layout->addMenuItem(['Input Fields'], [$path . 'field2'], $in);
$layout->addMenuItem('Input Field Decoration', [$path . 'field'], $in);
$layout->addMenuItem(['Checkboxes'], [$path . 'checkbox'], $in);
$layout->addMenuItem(['Value Selectors'], [$path . 'form6'], $in);
$layout->addMenuItem(['Lookup'], [$path . 'lookup'], $in);
$layout->addMenuItem(['Lookup Dependency'], [$path . 'lookup-dep'], $in);
$layout->addMenuItem(['DropDown'], [$path . 'dropdown-plus'], $in);
$layout->addMenuItem(['File Upload'], [$path . 'upload'], $in);
$layout->addMenuItem(['Multi Line'], [$path . 'multiline'], $in);
$layout->addMenuItem(['Tree Selector'], [$path . 'tree-item-selector'], $in);

$path = $demosUrl . 'collection/';
$g_t = $layout->addMenuGroup(['Data Collection', 'icon' => 'table']);
$layout->addMenuItem(['Actions - Integration Examples'], [$path . 'actions'], $g_t);
$layout->addMenuItem('Data table with formatted columns', [$path . 'table'], $g_t);
$layout->addMenuItem(['Advanced table examples'], [$path . 'table2'], $g_t);
$layout->addMenuItem('Table interractions', [$path . 'multitable'], $g_t);
$layout->addMenuItem(['Column Menus'], [$path . 'tablecolumnmenu'], $g_t);
$layout->addMenuItem(['Column Filters'], [$path . 'tablefilter'], $g_t);
$layout->addMenuItem('Grid - Table+Bar+Search+Paginator', [$path . 'grid'], $g_t);
$layout->addMenuItem('CRUD - Full editing solution', [$path . 'crud'], $g_t);
$layout->addMenuItem(['CRUD with Array Persistence'], [$path . 'crud3'], $g_t);
$layout->addMenuItem(['Lister'], [$path . 'lister-ipp'], $g_t);
$layout->addMenuItem(['Table column decorator from model'], [$path . 'tablecolumns'], $g_t);
$layout->addMenuItem(['Drag n Drop sorting'], [$path . 'jssortable'], $g_t);

$path = $demosUrl . 'interactive/';
$adv = $layout->addMenuGroup(['Interactive', 'icon' => 'talk']);
$layout->addMenuItem('Tabs', [$path . 'tabs'], $adv);
$layout->addMenuItem('Card', [$path . 'card'], $adv);
$layout->addMenuItem(['Accordion'], [$path . 'accordion'], $adv);
$layout->addMenuItem(['Wizard'], [$path . 'wizard'], $adv);
$layout->addMenuItem(['Virtual Page'], [$path . 'virtual'], $adv);
$layout->addMenuItem('Modal', [$path . 'modal'], $adv);
$layout->addMenuItem(['Loader'], [$path . 'loader'], $adv);
$layout->addMenuItem(['Console'], [$path . 'console'], $adv);
$layout->addMenuItem(['Dynamic scroll'], [$path . 'scroll-lister'], $adv);
$layout->addMenuItem(['Background PHP Jobs (SSE)'], [$path . 'sse'], $adv);
$layout->addMenuItem(['Progress Bar'], [$path . 'progress'], $adv);
$layout->addMenuItem(['Pop-up'], [$path . 'popup'], $adv);
$layout->addMenuItem(['Toast'], [$path . 'toast'], $adv);
$layout->addMenuItem('Paginator', [$path . 'paginator'], $adv);

$path = $demosUrl . 'javascript/';
$js = $layout->addMenuGroup(['Javascript', 'icon' => 'code']);
$layout->addMenuItem('Events', [$path . 'js'], $js);
$layout->addMenuItem('Element Reloading', [$path . 'reloading'], $js);
$layout->addMenuItem('Vue Integration', [$path . 'vue-component'], $js);

$path = $demosUrl . 'others/';
$other = $layout->addMenuGroup(['Others', 'icon' => 'plus']);
$layout->addMenuItem('Sticky GET', [$path . 'sticky'], $other);
$layout->addMenuItem('More Sticky', [$path . 'sticky2'], $other);
$layout->addMenuItem('Recursive Views', [$path . 'recursive'], $other);

// view demo source page on Github
\atk4\ui\Button::addTo($layout->menu->addItem()->addClass('aligned right'), ['View Source', 'teal', 'icon' => 'github'])
    ->on('click', $app->jsRedirect('https://github.com/atk4/ui/blob/develop/' . $relUrl, true));
}
