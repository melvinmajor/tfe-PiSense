# ATK UI - Collection of interractive Web App UI components

Do you use a CSS framework?

ATK UI began 2 years ago as a project to wrap CSS framework into PHP objects. We started with Fields, Forms, Tables and Menus. Then we added callbacks to enable interactivity. Then came layouts. Next we integrated data mapping and persistence frameworks; virtual pages and JavaScript action abstraction; dynamic popups, real-time console and progress-bar. We have even added high-level components such as wizard and dynamic tabs and make them extremely simple to use!

**ATK UI helps you create modern Web UI without writing HTML/CSS/JS. Bundled with over 30 interactive UI components that seamlessly integrate with your SQL, NoSQL or API backend, ATK UI brings consistent and easy-to-use interface into your custom projects or popular web apps**

[![Build Status](https://travis-ci.org/atk4/ui.png?branch=develop)](https://travis-ci.org/atk4/ui)
[![Code Climate](https://codeclimate.com/github/atk4/ui/badges/gpa.svg)](https://codeclimate.com/github/atk4/ui)
[![StyleCI](https://styleci.io/repos/68417565/shield)](https://styleci.io/repos/68417565)
[![codecov](https://codecov.io/gh/atk4/ui/branch/develop/graph/badge.svg)](https://codecov.io/gh/atk4/ui)
[![GitHub release](https://img.shields.io/github/release/atk4/ui.svg)](CHANGELOG.md)

Quick-Links: [Documentation](https://agile-ui.readthedocs.io). [Namespaces](https://www.agiletoolkit.org/dox/namespaces.html). [Demo-site](https://ui.agiletoolkit.org). [ATK Data](https://github.com/atk4/data). [Forum](https://forum.agiletoolkit.org/). [Chat](https://gitter.im/atk4/atk4). [Commercial support](https://www.agiletoolkit.org/contact). [Udemy Course](https://forum.agiletoolkit.org/t/udemy-com-atk-course-early-access-limited-time-free/413).

## ATK UI is simple and really saves time

Some of you "live to code". For everyone else - ATK UI is a great fit. Easy to learn and saves loads of time.

Watch this free Udemy course if you are a beginner: https://www.udemy.com/web-apps-with-php-and-atk

### How does it work?

Download from www.agiletoolkit.org or Install ATK UI with `composer require atk4/ui`

Then you only need to create a single PHP file:

``` php
<?php
require 'vendor/autoload.php';

$app = new \atk4\ui\App();   // That's your UI application
$app->initLayout('Centered');

$form = new \atk4\ui\Form(); // Yeah, that's a form!
$app->add($form);

$form->addField('email');    // adds field
$form->onSubmit(function ($form) {
    // implement subscribe here

    return $form->success('Subscribed '.$form->model['email'].' to newsletter.');
});

// Decorate anything
$form->buttonSave->set('Subscribe');
$form->buttonSave->icon = 'mail';

// everything renders automatically
```

Open PHP in the browser and observe a fully working and good looking form:

![subscribe](docs/images/subscribe.png)

ATK UI relies on https://fomantic-ui.com CSS framework to render the form beautifully. It also implements submission call-back in a very straightforward way. The demo also demonstrates use of JavaScript action, which can make objects interract with each-other (e.g. Form submit reloads Table).

### Database Integration with ATK Data

To get most of ATK UI, use [ATK Data](https://github.com/atk4/data) to describe your business models such as "User" or "Purchase". When you define models, you can start using some more advanced components:

[CRUD](https://ui.agiletoolkit.org/demos/crud.php) is a fully-interractive component that supports pagination, reloading, conditions, data formatting, sorting, quick-search, ordering, custom actions and modals, but at the same time is very easy to use:

``` php
$app = new \atk4\ui\App('hello world');
$app->initLayout('Admin');
$app->dbConnect('mysql://user:pass@localhost/atk')

$app->add('CRUD')->setModel(new User($app->db));
```

ATK Data allows you to set up relations between models:

``` php
class User extends Model {
    function init() {
        parent::init();

        $this->addField('name');
        $this->addField('gender', ['enum'=>'female','male','other']);
        $this->hasMany('Purchases', new Purchase());
    }
}
```

Conventional CRUD works only with a single model, but with add-on you can take advantage this relationship information: https://github.com/atk4/mastercrud

``` php
use \atk4\mastercrud\MasterCRUD;

// set up $app here

$master_crud = $app->add(new MasterCRUD());
$master_crud->setModel(new User($app->db), [
  'Purchases'=>[]
]);

```

## Styling

ATK UI uses default settings from Formantic-UI but they can be styled. You can create custom application layouts, page layouts and widgets. Here is a screenshot of an application developed using ATK UI:

![subscribe](docs/images/saasty.png)

## Callbacks. Callbacks everywhere!

One of the fundamental features of ATK is Callback - ability to dynamically generate a route then have JS part of the component invoke it. Thanks to this approach, code can be fluid, simple and readable:

``` php
$tabs = $app->add('Tabs');
$tabs->addTab('Intro')->add(['Message', 'Other tabs are loaded dynamically!']);

$tabs->addTab('Users', function($p) use($app) {

    // This tab is loaded dynamically, but also contains dynamic component
    $p->add('CRUD')->setModel(new User($app->db));
});

$tabs->addTab('Settings', function($p) use($app) {

    // Second tab contains an AJAX form that stores itself back to DB.
    $m = new Settings($app->db);
    $m->load(2);
    $p->add('Form')->setModel($m);
});
```

## Wizard

Another component implementation using a very friendly PHP syntax:

![wizard](docs/images/wizard.png)

You get most benefit when you use various ATK UI Components together. Try the following demo: https://ui.agiletoolkit.org/demos/wizard.php. The demo implements:

-   Multi-step wizard with ability to navigate forward and backward
-   Form with validation
-   Data memorization in the session
-   Table with column formatter, Messages
-   Real-time output console

With ATK it [takes about 50 lines of PHP code only](https://github.com/atk4/ui/blob/develop/demos/wizard.php) to build it all.

## ATK UI is part of [Agile Toolkit](https://agiletoolkit.org/)

Comparing to some other CRUD / Admin builders, the UI components rely on a very powerful ATK Data framework, which can be also used separately and can be used to power your [RestAPI](https://github.com/atk4/api) end-points.

See how ATK Data compares with other ORM engines and you'll understand why we choose it over some of the alternatives: http://socialcompare.com/en/comparison/php-data-access-libraries-orm-activerecord-persistence

To help you understand the real power behind ATK Data integration, look at this aggregation / reporting addon: https://github.com/atk4/report. Compared to any open-source report suites that you can find for PHP, this is the only implementation that relies on "Model Domain Logic" rather than SQL queries for expressing your report criteria and can be used for ANY component in ATK UI as well as addons, such as [Charts](https://github.com/atk4/chart). There are no performance implications, because all the expressions and aggregations are executed inside your database through the means of SQL.

## ATK is commercially supported

The MIT license gives you absolute freedom, but no warranty. To compliment that, the team who created ATK as well as some early contributors joined together to run a consultancy company. We help you deliver your projects by:

-   Fixing bugs in ATK or add-ons - free of charge
-   Building add-ons that extend functionality - moderate hourly rate fee.
-   Integration tasks or building parts of your project - quotation based.

Our motto is to "always give back to open-source community and be fair to our clients". We are hiring PHP and JavaScript developers who are passionate about ATK and are active within our community.

If you need a help, go to [our website](https://www.agiletoolkit.org) and click on "Contact" link.

## Getting Started: Build your admin

It's really easy to put together a complex Admin system. Add this code to a new PHP file (tweak it with your database details, table and fields):

``` php
<?php

  $app = new \atk4\ui\App('My App');
  $app->initLayout('Admin');
  $app->dbConnect('mysql://user:pass@localhost/yourdb');

  class User extends \atk4\data\Model {
      public $table = 'user';
      function init() {
          parent::init();

          $this->addField('name');
          $this->addField('email', ['required'=>true]);
          $this->addField('password', ['type'=>'password']);
      }
  }

  $app->add('CRUD')->setModel(new User($app->db));
```

The result is here:

![](docs/images/admin-in-15-lines.png)

## Bundled and Planned components

Agile UI comes with many built-in components:

| Component                                                    | Description                                                  | Introduced |
| ------------------------------------------------------------ | ------------------------------------------------------------ | ---------- |
| [View](https://ui.agiletoolkit.org/demos/view.php)            | Template, Render Tree and various patterns                   | 0.1        |
| [Button](https://ui.agiletoolkit.org/demos/button.php)        | Button in various variations including icons, labels, styles and tags | 0.1        |
| [Input](https://ui.agiletoolkit.org/demos/field.php)          | Decoration of input fields, integration with buttons.        | 0.2        |
| [JS](https://ui.agiletoolkit.org/demos/button2.php)           | Assign JS events and abstraction of PHP callbacks.           | 0.2        |
| [Header](https://ui.agiletoolkit.org/demos/header.php)        | Simple view for header.                                      | 0.3        |
| [Menu](https://ui.agiletoolkit.org/demos/layout2.php)         | Horizontal and vertical multi-dimensional menus with icons.  | 0.4        |
| [Form](https://ui.agiletoolkit.org/demos/form.php)            | Validation, Interactivity, Feedback, Layouts, Field types.   | 0.4        |
| [Layouts](https://ui.agiletoolkit.org/demos/layouts.php)      | Admin, Centered.                                             | 0.4        |
| [Table](https://ui.agiletoolkit.org/demos/table.php)          | Formatting, Columns, Status, Link, Template, Delete.         | 1.0        |
| [Grid](https://ui.agiletoolkit.org/demos/grid.php)            | Toolbar, Paginator, Quick-search, Expander, Actions.         | 1.1        |
| [Message](https://ui.agiletoolkit.org/demos/message.php)      | Such as "Info", "Error", "Warning" or "Tip" for easy use.    | 1.1        |
| [Modal](https://ui.agiletoolkit.org/demos/modal.php)         | Modal dialog with dynamically loaded content.                | 1.1        |
| [Reloading](https://ui.agiletoolkit.org/demos/reloading.php)  | Dynamically re-render part of the UI.                        | 1.1        |
| [Actions](https://ui.agiletoolkit.org/demos/reloading.php)   | Extended buttons with various interactions                   | 1.1        |
| [CRUD](https://ui.agiletoolkit.org/demos/crud.php)            | Create, List, Edit and Delete records (based on Advanced Grid) | 1.1        |
| [Tabs](https://ui.agiletoolkit.org/demos/tabs.php)           | 4 Responsive: Admin, Centered, Site, Wide.                   | 1.2        |
| [Loader](https://ui.agiletoolkit.org/demos/loader.php)        | Dynamically load itself and contained components inside.     | 1.3        |
| [Modal View](https://ui.agiletoolkit.org/demos/modal2.php)    | Open/Load contained components in a dialog.                  | 1.3        |
| [Breadcrumb](https://ui.agiletoolkit.org/demos/breadcrumb.php) | Push links to pages for navigation. Wizard.                  | 1.4        |
| [ProgressBar](https://ui.agiletoolkit.org/demos/progress.php) | Interactive display of a multi-step PHP code execution progress | 1.4        |
| [Console](https://ui.agiletoolkit.org/demos/console.php)      | Execute server/shell commands and display progress live      | 1.4        |
| [Items and Lists](https://ui.agiletoolkit.org/demos/lister.php) | Flexible and high-performance way to display lists of items. | 1.4        |
| [Wizard](https://ui.agiletoolkit.org/demos/wizard.php)        | Multi-step, wizard with temporary data storing.              | 1.4        |
|                                                              |                                                              |            |

## Add-ons and integrations

Add-ons:

-   [MasterCRUD](https://github.com/atk4/mastercrud) - Create multi-level CRUD system with BreadCrumb
-   [Filestore](https://github.com/atk4/filestore) - Integrate your Form with Flysystem, uploading and storing files
-   [User Authentication](https://github.com/atk4/login) - User Log-in, Registration and Access Control for Agile UI
-   [Charts add-on](https://github.com/atk4/chart) - Modern looking and free charts with [chartJS](https://www.chartjs.org/)
-   [Audit for Models](https://github.com/atk4/audit) - Record all DB operations with Undo/Redo support for Agile Data
-   [Data for Reports](https://github.com/atk4/report) - Implement data aggregation and union models for Agile Data
-   [Schema and Migration](https://github.com/atk4/schema) - Tools to migrate your database structure

Integrations:

-   [Agile UI for Wordpress](https://github.com/ibelar/atk-wordpress) - Write Wordpress plugin using Agile UI
-   [Laravel Agile Data](https://github.com/atk4/laravel-ad) - ServiceProvider for Agile Data
-   .. more connectors wanted. If you are working to integrate Agile UI or Agile Data, please list it here (even if incomplete).



All bundled components are free and licensed under MIT license. They are installed together with Agile UI.

External and 3rd party components may be subject to different licensing terms.

## Documentation and Community

ATK UI makes active use of ATK Core and ATK Data frameworks.

-   [Agile UI Documentation](https://agile-ui.readthedocs.io)
-   [Agile Data Documentation](https://agile-data.readthedocs.io)
-   [Agile Core Documentation](https://agile-core.readthedocs.io)

## ATK UI Schematic

![agile-ui](docs/images/agile-ui.png)

## Credits and License

Agile UI, Data and API are projects we develop in our free time and offer you free of charge under terms of MIT license. If you wish to say thanks to our core team or take part in the project, please contact us through our chat on Gitter.


[![Gitter](https://img.shields.io/gitter/room/atk4/atk4.svg?maxAge=2592000)](https://gitter.im/atk4/atk4?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
