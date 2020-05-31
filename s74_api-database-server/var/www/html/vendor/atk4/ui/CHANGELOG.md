# Change Log

# 1.6 Release

Our 1.6 release signifies a switch from a stale Semantic UI CSS framework to a community-supported [Fomantic UI](https://fomantic-ui.com/) fork, version 2.6.2. Calendar widget is now supported natively, various other issues are addressed but, more importantly, Fomantic UI is open for collaboration. We are working with their core maintainers [hammy2899](https://github.com/hammy2899), [prudho](https://github.com/prudho) and [ColinFrick](https://github.com/ColinFrick) to implement new modern UI features.

Also now have ability to resize any table, Grid or CRUD:

``` php

// Enable reizable columns on the table
$table->resizableColumn();

// Also supports custom callaback (on-resize) and ability to pre-set the width:
$table->resizableColumn(function($j, $w){
    $columnWidths = json_decode($w);
    // store widths somewhere
    return;
}, [200,300,100,100,100]); // default widths

[$table->resizableColumns();](https://agile-ui.readthedocs.io/en/latest/table.html?highlight=table#resizable-columns)


// For Grid or CRUD:
$crud->table->resizableColumn
```

We also introducing a somewhat experemental "Lookup" field. It is identical to AutoComplete and can work as a stand-in
replacement, but supports "filters". For now we are looking for ways to make this field more compact before it becomes
part of AutoComplete.

``` php

$form = $app->add(new \atk4\ui\Form(['segment']));
$form->add(['Label', 'Add city', 'top attached'], 'AboveFields');

$l = $form->addField('city',['Lookup']);

// will restraint possible city value in droddown base on country and/or language.
$l->addFilter('country', 'Country');
$l->addFilter('language', 'Lang');

//make sure country and language belong to your model.
$l->setModel(new City($db));
```

## 1.6.0

**Closed issues:**

- Menu-\>addItem is not using App-\>url method [\#565](https://github.com/atk4/ui/issues/565)
- $grid-\>jsReload\(\) does not show loading indicator [\#561](https://github.com/atk4/ui/issues/561)
- In CRUD form-\>error\(\) is not treated correctly [\#558](https://github.com/atk4/ui/issues/558)
- DropDown with icons don't look nice [\#514](https://github.com/atk4/ui/issues/514)
- jsNotify-\>setDuration\(0\) message is not sown forever [\#478](https://github.com/atk4/ui/issues/478)
- Paginator should have ability to choose items per page [\#441](https://github.com/atk4/ui/issues/441)

**Merged pull requests:**

- Set view as not rendered if there is exception [\#569](https://github.com/atk4/ui/pull/569) (@DarkSide666)
- fix/ grid table overflow [\#568](https://github.com/atk4/ui/pull/568) (@ibelar)
- Upgrading to Fomantic-UI [\#567](https://github.com/atk4/ui/pull/567) (@romaninsh)
- fix \#565, Menu-\>addItem URL treatment [\#566](https://github.com/atk4/ui/pull/566) (@DarkSide666)
- feature/leftMenu option [\#564](https://github.com/atk4/ui/pull/564) (@ibelar)
- Feature/js search auto query [\#563](https://github.com/atk4/ui/pull/563) (@ibelar)
- $grid-\>jsReload not showing reload indicator [\#562](https://github.com/atk4/ui/pull/562) (@skondakov)
- Feature/form submit hook in crud [\#560](https://github.com/atk4/ui/pull/560) (@DarkSide666)
- Feature/Table Column Resizable [\#559](https://github.com/atk4/ui/pull/559) (@ibelar)
- Fix LoremIpsum constructor \(seed mechanism\) [\#556](https://github.com/atk4/ui/pull/556) (@DarkSide666)
- Feature/fix on change radio [\#555](https://github.com/atk4/ui/pull/555) (@DarkSide666)
- add scrollbar if needed in modal [\#553](https://github.com/atk4/ui/pull/553) (@DarkSide666)
- typo fix [\#552](https://github.com/atk4/ui/pull/552) (@DarkSide666)
- Feature/support for serialized fields [\#548](https://github.com/atk4/ui/pull/548) (@romaninsh)
- Implements FormField-\>onChange method [\#547](https://github.com/atk4/ui/pull/547) (@DarkSide666)
- console-\>set\(\) captures executon info/output. [\#546](https://github.com/atk4/ui/pull/546) (@romaninsh)

## 1.6.1

Adding new form layouts, toast and switching to selenium for UI tests. Fixed readonly fields, added many tests
and upgraded Fomantic-UI version. 


**Closed issues:**

- Can not tick CheckBox field when it is in group [\#593](https://github.com/atk4/ui/issues/593)
- Message view Icon placement is strange [\#592](https://github.com/atk4/ui/issues/592)
- Modal without any action buttons at the bottom still shows action div [\#583](https://github.com/atk4/ui/issues/583)
- DropDown doesn't open when having showOnFocus=false [\#580](https://github.com/atk4/ui/issues/580)
- Feature Request: JsReload should be able to set .api parameters \(like Form does\) [\#578](https://github.com/atk4/ui/issues/578)
- Disabled fields can still be focused and edited [\#575](https://github.com/atk4/ui/issues/575)
- Grid sorting memorizes previous sort order in URL [\#573](https://github.com/atk4/ui/issues/573)
- Columns not sorting if added after setModel [\#544](https://github.com/atk4/ui/issues/544)
- Calendar form field on\('change'\) event handler not working [\#521](https://github.com/atk4/ui/issues/521)
- Upload FormField: Setting placeholder via placeholder does not work [\#483](https://github.com/atk4/ui/issues/483)
- Placeholder is not picked up from Model field ui\[placeholder\] property [\#468](https://github.com/atk4/ui/issues/468)
- type='money' is no longer using 'Money' column decorarot [\#414](https://github.com/atk4/ui/issues/414)

**Merged pull requests:**

- fix DropDown field HTML markup \(getTag usage\) [\#604](https://github.com/atk4/ui/pull/604) (@DarkSide666)
- fix/Readonly state for Lookup and Autocomplete [\#603](https://github.com/atk4/ui/pull/603) (@ibelar)
- fix/Dropdown multiple value [\#602](https://github.com/atk4/ui/pull/602) (@ibelar)
- Feature/form section [\#600](https://github.com/atk4/ui/pull/600) (@ibelar)
- Feature/accordion only [\#599](https://github.com/atk4/ui/pull/599) (@ibelar)
- Feature/fix add column [\#597](https://github.com/atk4/ui/pull/597) (@DarkSide666)
- don't show never\_persist fields as changed - that's confusing [\#596](https://github.com/atk4/ui/pull/596) (@DarkSide666)
- Switched to selenium... and it works, YEY [\#595](https://github.com/atk4/ui/pull/595) (@romaninsh)
- Feature/Toast Module - Fomantic 2.6.4 Release [\#594](https://github.com/atk4/ui/pull/594) (@ibelar)
- fix demo database [\#591](https://github.com/atk4/ui/pull/591) (@DarkSide666)
- fix/lister demo template [\#588](https://github.com/atk4/ui/pull/588) (@ibelar)
- Feature/dynamic scroll [\#587](https://github.com/atk4/ui/pull/587) (@ibelar)
- Feature/demo lineend bug [\#586](https://github.com/atk4/ui/pull/586) (@DarkSide666)
- Fix/\#573 Memorize url [\#585](https://github.com/atk4/ui/pull/585) (@ibelar)
- Only show action \<div\> if there are actions [\#584](https://github.com/atk4/ui/pull/584) (@PhilippGrashoff)
- Feature/jsReload with semantic configurable api options. [\#582](https://github.com/atk4/ui/pull/582) (@ibelar)
- add formConfig to pass parameters to FUI .form\(\) [\#581](https://github.com/atk4/ui/pull/581) (@PhilippGrashoff)
- Allow PHP 7.2+ to check method signatures [\#579](https://github.com/atk4/ui/pull/579) (@romaninsh)
- View::on now takes callback and JS actions [\#577](https://github.com/atk4/ui/pull/577) (@PhilippGrashoff)
- fix/ set input attribute to readonly or disable. [\#576](https://github.com/atk4/ui/pull/576) (@ibelar)
- Sorting should happen later. Fix \#544 [\#574](https://github.com/atk4/ui/pull/574) (@DarkSide666)

## 1.6.2

**Closed issues:**

- dev-develop branches should require other dev-develop branches for all atk/\* repositories [\#641](https://github.com/atk4/ui/issues/641)
- mink-selenium2-driver [\#638](https://github.com/atk4/ui/issues/638)
- hasOne relation should use Lookup field instead of DropDown by default [\#636](https://github.com/atk4/ui/issues/636)
- Template: tryAppend\(\) and tryAppendHTML\(\) woud be nice [\#626](https://github.com/atk4/ui/issues/626)
- Possible Restriction Solution: custom templates must be defined in vendor atk4/ui  [\#625](https://github.com/atk4/ui/issues/625)
- CRUD and read\_only field [\#620](https://github.com/atk4/ui/issues/620)
- Ternary operator messes up integer with value = 0 in DropDown::getInput\(\) [\#618](https://github.com/atk4/ui/issues/618)
- Template \_top tag doesn't work [\#610](https://github.com/atk4/ui/issues/610)
- Multiple Modals: Closing second modal by clicking dimmer does not work everywhere in dimmer if second modal is smaller than first one [\#609](https://github.com/atk4/ui/issues/609)
- Lister need {empty} ability in it's template [\#606](https://github.com/atk4/ui/issues/606)
- confirm in View-\>on\(\) only works with Callback, not with jsReload for example [\#503](https://github.com/atk4/ui/issues/503)
- Font size on tablet screen [\#485](https://github.com/atk4/ui/issues/485)
- Implement a simple template routing in Agile UI [\#440](https://github.com/atk4/ui/issues/440)
- Nested Modals [\#436](https://github.com/atk4/ui/issues/436)
- Integrate ATK with Zend3 [\#384](https://github.com/atk4/ui/issues/384)

**Merged pull requests:**

- Add "confirm" option support for js-\>on\(\) case [\#642](https://github.com/atk4/ui/pull/642) (@DarkSide666)
- Feature/test persist array [\#640](https://github.com/atk4/ui/pull/640) (@DarkSide666)
- fix \#638 [\#639](https://github.com/atk4/ui/pull/639) (@DarkSide666)
- Use Lookup field for hasOne, fix \#636 [\#637](https://github.com/atk4/ui/pull/637) (@DarkSide666)
- Don't apply sorting in case sortable=false. [\#635](https://github.com/atk4/ui/pull/635) (@DarkSide666)
- Feature/fix paginator plus sort [\#634](https://github.com/atk4/ui/pull/634) (@DarkSide666)
- Feature/upd crud demo [\#633](https://github.com/atk4/ui/pull/633) (@DarkSide666)
- fix in case you don't have {empty} tag in your Lister template [\#632](https://github.com/atk4/ui/pull/632) (@DarkSide666)
- add ability to set buttonSave=false to not create it [\#631](https://github.com/atk4/ui/pull/631) (@DarkSide666)
- comment fix [\#630](https://github.com/atk4/ui/pull/630) (@DarkSide666)
- Feature/allow to set multiple template paths [\#628](https://github.com/atk4/ui/pull/628) (@DarkSide666)
- Refactor Template set, append, implement tryAppend, add tests. [\#627](https://github.com/atk4/ui/pull/627) (@DarkSide666)
- Fix UTF-8 [\#624](https://github.com/atk4/ui/pull/624) (@mvorisek)
- Critical bug fix: always convert Dropdown/Autocomplete/Lookup values to string [\#623](https://github.com/atk4/ui/pull/623) (@mvorisek)
- Fixes \#618: Process fields with integer value 0 correctly [\#619](https://github.com/atk4/ui/pull/619) (@PhilippGrashoff)
- Feature/dynamic scroll crud [\#617](https://github.com/atk4/ui/pull/617) (@DarkSide666)
- Make demo for dynamic scroll in container better [\#616](https://github.com/atk4/ui/pull/616) (@DarkSide666)
- Feature/cond form fixes [\#615](https://github.com/atk4/ui/pull/615) (@DarkSide666)
- Feature/add dropdown tests [\#614](https://github.com/atk4/ui/pull/614) (@romaninsh)
- feature/upgrade js package dependencies [\#613](https://github.com/atk4/ui/pull/613) (@ibelar)
- Feature/refactor template [\#612](https://github.com/atk4/ui/pull/612) (@DarkSide666)
- Implements {empty} tag in Lister [\#611](https://github.com/atk4/ui/pull/611) (@DarkSide666)
- Fix mobile layout [\#608](https://github.com/atk4/ui/pull/608) (@skondakov)
- Catch coverage usage from callback handlers executed from php shutdown [\#607](https://github.com/atk4/ui/pull/607) (@romaninsh)

## 1.6.3

**Closed issues:**

- Table using setSource\(\): Ability to add custom headline [\#650](https://github.com/atk4/ui/issues/650)
- Double confirm alert [\#649](https://github.com/atk4/ui/issues/649)
- Form - create "read-only" field [\#486](https://github.com/atk4/ui/issues/486)

**Merged pull requests:**

- Update UI.php to let dev decide about naming [\#657](https://github.com/atk4/ui/pull/657) (@FabulousGee)
- Removed deprecated each functions from template parsing [\#654](https://github.com/atk4/ui/pull/654) (@abbadon1334)
- fix/\#649 Double Confirm [\#653](https://github.com/atk4/ui/pull/653) (@ibelar)
- make templates-dir easier to customize [\#648](https://github.com/atk4/ui/pull/648) (@DarkSide666)
- Atk4/ui/table column image [\#645](https://github.com/atk4/ui/pull/645) (@skondakov)
- bugfix for confirm [\#644](https://github.com/atk4/ui/pull/644) (@DarkSide666)

## 1.6.4

**Merged pull requests:**

- Feature/implement grid layout [\#662](https://github.com/atk4/ui/pull/662) (@DarkSide666)
- fix Lister [\#660](https://github.com/atk4/ui/pull/660) (@DarkSide666)
- Feature/fix lister render [\#659](https://github.com/atk4/ui/pull/659) (@DarkSide666)

## 1.6.5

**Closed issues:**

- Bad icon and text button vertical alignment [\#668](https://github.com/atk4/ui/issues/668)
- Incorrect ids for elements [\#667](https://github.com/atk4/ui/issues/667)
- Width is not applied correctly to elements [\#656](https://github.com/atk4/ui/issues/656)
- Improvements to Console [\#392](https://github.com/atk4/ui/issues/392)

**Merged pull requests:**

- Feature/Fomantic Update [\#675](https://github.com/atk4/ui/pull/675) (@ibelar)
- fix/Radio Input Field [\#674](https://github.com/atk4/ui/pull/674) (@ibelar)
- fix/jsNotifier Transition [\#673](https://github.com/atk4/ui/pull/673) (@ibelar)
- fix/Lookup Autocomplete fixes [\#671](https://github.com/atk4/ui/pull/671) (@ibelar)
- Add container for action buttons [\#669](https://github.com/atk4/ui/pull/669) (@DarkSide666)
- revert lister template changes [\#666](https://github.com/atk4/ui/pull/666) (@DarkSide666)
- Defines which Table Decorator to use for Actions [\#665](https://github.com/atk4/ui/pull/665) (@gowrav-vishwakarma)

# 1.5 Release

*Improved Admin Layout*: now is fully flexible and more responsive.

Grids can now have items per page configured:

``` php
$grid->addItemsPerPageSelector();
```

Filter grid by placing a pop-up in it's column header ([demo](http://ui.agiletoolkit.org/demos/tablefilter.php):

``` php
$grid->addFilterColumn();
```


## 1.5.0

**Closed issues:**

- Why Dropdown field doesn't care about model field default value [\#463](https://github.com/atk4/ui/issues/463)
- Placing Form Fields inside layouts: Add "Region" parameter to addField [\#444](https://github.com/atk4/ui/issues/444)
- Autocomplete does not load values when in CRUD's modal: [\#438](https://github.com/atk4/ui/issues/438)
- AutoComplete does not work within Wizard [\#429](https://github.com/atk4/ui/issues/429)
- ApiService sometimes throw alert if the DIV is being removed while loading [\#421](https://github.com/atk4/ui/issues/421)
- Form Dropdowns do not show ... \(empty selection\) once something is selected [\#418](https://github.com/atk4/ui/issues/418)

**Merged pull requests:**

- Feature/admin layout2 [\#460](https://github.com/atk4/ui/pull/460) ([romaninsh](https://github.com/romaninsh))
- add warnings in Console tests for Windows [\#459](https://github.com/atk4/ui/pull/459) ([DarkSide666](https://github.com/DarkSide666))
- Feature/autocomplete custom titlefield [\#457](https://github.com/atk4/ui/pull/457) ([DarkSide666](https://github.com/DarkSide666))
- Added setApiConfig\(\) function to Form.php [\#455](https://github.com/atk4/ui/pull/455) ([PhilippGrashoff](https://github.com/PhilippGrashoff))
- feature/async-defer [\#454](https://github.com/atk4/ui/pull/454) ([ibelar](https://github.com/ibelar))
- Fix/\#452 Grid action button [\#453](https://github.com/atk4/ui/pull/453) ([ibelar](https://github.com/ibelar))
- feature/Grid-ipp-selector [\#450](https://github.com/atk4/ui/pull/450) ([ibelar](https://github.com/ibelar))
- Feature/Table-Column-Filter [\#448](https://github.com/atk4/ui/pull/448) ([ibelar](https://github.com/ibelar))
- Fix/Crud Draghandler Position [\#442](https://github.com/atk4/ui/pull/442) ([ibelar](https://github.com/ibelar))
- semantic-ui release update [\#439](https://github.com/atk4/ui/pull/439) ([ibelar](https://github.com/ibelar))
- Allow to set a custom Limit to loaded records [\#435](https://github.com/atk4/ui/pull/435) ([PhilippGrashoff](https://github.com/PhilippGrashoff))
- Feature/Table Column Menu [\#432](https://github.com/atk4/ui/pull/432) ([ibelar](https://github.com/ibelar))
- Fix \#418 [\#431](https://github.com/atk4/ui/pull/431) ([PhilippGrashoff](https://github.com/PhilippGrashoff))
- Allow to override url setting [\#430](https://github.com/atk4/ui/pull/430) ([PhilippGrashoff](https://github.com/PhilippGrashoff))
- Fixed typo placholder =\> placeholder [\#428](https://github.com/atk4/ui/pull/428) ([PhilippGrashoff](https://github.com/PhilippGrashoff))
- feature/dynamic-popup [\#427](https://github.com/atk4/ui/pull/427) ([ibelar](https://github.com/ibelar))
- feature/release-build [\#426](https://github.com/atk4/ui/pull/426) ([ibelar](https://github.com/ibelar))
- Adding virtualpage into Form makes sure we inherit stickyGet [\#425](https://github.com/atk4/ui/pull/425) ([romaninsh](https://github.com/romaninsh))
- Feature/Conditional Form [\#422](https://github.com/atk4/ui/pull/422) ([ibelar](https://github.com/ibelar))

### 1.5.1

**Closed issues:**

- demos/jscondform.php page is not added in demos left side menu [\#469](https://github.com/atk4/ui/issues/469)
- case-sensitive issue [\#464](https://github.com/atk4/ui/issues/464)

**Merged pull requests:**

- bugfix if icon is not set [\#475](https://github.com/atk4/ui/pull/475) ([DarkSide666](https://github.com/DarkSide666))
- Feature/Dropdown-Plus [\#474](https://github.com/atk4/ui/pull/474) ([ibelar](https://github.com/ibelar))
- fix/issue\#466 [\#472](https://github.com/atk4/ui/pull/472) ([ibelar](https://github.com/ibelar))
- add conditional field demo in menu [\#471](https://github.com/atk4/ui/pull/471) ([DarkSide666](https://github.com/DarkSide666))
- fix 464 [\#470](https://github.com/atk4/ui/pull/470) ([DarkSide666](https://github.com/DarkSide666))
- Feature/form custom layout [\#467](https://github.com/atk4/ui/pull/467) ([romaninsh](https://github.com/romaninsh))

### 1.5.2

**Closed issues:**

- Class 'atk4\ui\FormLayout\Exception' not found \(in \_Abstract.php\) [\#480](https://github.com/atk4/ui/issues/480)
- App-\>initLayout 2nd parameter $options [\#476](https://github.com/atk4/ui/issues/476)
- allow to use icon in DropDown [\#473](https://github.com/atk4/ui/issues/473)
- implement custom form layout [\#465](https://github.com/atk4/ui/issues/465)
- TableColumn/Link doesn't evaluate as template [\#462](https://github.com/atk4/ui/issues/462)

**Merged pull requests:**

- feature/jsSearch-Initial-Value [\#490](https://github.com/atk4/ui/pull/490) ([ibelar](https://github.com/ibelar))
- fix \#476 [\#489](https://github.com/atk4/ui/pull/489) ([DarkSide666](https://github.com/DarkSide666))
- Fix/AutoComplete [\#488](https://github.com/atk4/ui/pull/488) ([ibelar](https://github.com/ibelar))
- Allow $field-\>ui\['placeholder'\] to affect form fields' placeholder directly. [\#484](https://github.com/atk4/ui/pull/484) ([romaninsh](https://github.com/romaninsh))
- Make View-\>on\(event, \[jsAction1, jsAction2\]\) work [\#482](https://github.com/atk4/ui/pull/482) ([PhilippGrashoff](https://github.com/PhilippGrashoff))
- Fix/\#480 Unable to find Exception [\#481](https://github.com/atk4/ui/pull/481) ([ibelar](https://github.com/ibelar))
- Fix/\#478 Notify options [\#479](https://github.com/atk4/ui/pull/479) ([ibelar](https://github.com/ibelar))
- Fix/Dropdown search not showing input char [\#477](https://github.com/atk4/ui/pull/477) ([ibelar](https://github.com/ibelar))

### 1.5.3

This release addresses problem Semantic UI is having with multiple modals. We will now hide existing modal
if new one needs to be opened and after this new modal is closed it will be re-opened once again. The relevant
issue is [\#487](https://github.com/atk4/ui/issues/487)

**Closed issues:**

- Grid should have loader while reloading [\#493](https://github.com/atk4/ui/issues/493)

**Merged pull requests:**

- Grid will display loader while fetching data [\#497](https://github.com/atk4/ui/pull/497) ([ibelar](https://github.com/ibelar))
- fix/\#487-Modal Behaviour [\#496](https://github.com/atk4/ui/pull/496) ([ibelar](https://github.com/ibelar))
- fix/Persisitence-ui-for-reference-field [\#494](https://github.com/atk4/ui/pull/494) ([ibelar](https://github.com/ibelar))
- atk4/ui/fix/\#491 auto complete not initializing with proper text [\#492](https://github.com/atk4/ui/pull/492) ([skondakov](https://github.com/skondakov))

## 1.5.4

This issue fixed a problem where modal windows coulddn't open when placed in a dynamic tab. There
probably were some other situation where callbacks were not reachable, and this release should
address them.

**Closed issues:**

- Add Filter Type Money [\#517](https://github.com/atk4/ui/issues/517)
- UploadField: should open dialog on "click" too not only on "focus" [\#511](https://github.com/atk4/ui/issues/511)
- UploadField compatibility with multiple jQuery [\#510](https://github.com/atk4/ui/issues/510)
- Enhancement: Make dropdown options visible when clicking into empty area between text and dropdown icon [\#502](https://github.com/atk4/ui/issues/502)
- Modal in Tab not working [\#500](https://github.com/atk4/ui/issues/500)
- AutoComplete  not initializing with proper text [\#491](https://github.com/atk4/ui/issues/491)

**Merged pull requests:**

- Atk4/ui/fix/\#517 add filter type money [\#518](https://github.com/atk4/ui/pull/518) ([skondakov](https://github.com/skondakov))
- fix/\#510-\#511-file-upload [\#516](https://github.com/atk4/ui/pull/516) ([ibelar](https://github.com/ibelar))
- Fix/modal callback in modal callback [\#509](https://github.com/atk4/ui/pull/509) ([ibelar](https://github.com/ibelar))
- Feature/Calendar-input-using-ui-persistence [\#508](https://github.com/atk4/ui/pull/508) ([ibelar](https://github.com/ibelar))
- Fix problem with multiple callbacks being not executed [\#501](https://github.com/atk4/ui/pull/501) ([romaninsh](https://github.com/romaninsh))
- hotfix [\#499](https://github.com/atk4/ui/pull/499) ([romaninsh](https://github.com/romaninsh))

## 1.5.5

**Closed issues:**

- CRUD edit form reload don't use stickyGet parameters [\#524](https://github.com/atk4/ui/issues/524)
- If AJAX request fails - provide user-friendly way to see error [\#522](https://github.com/atk4/ui/issues/522)
- Date picker doesn't respect APP-\>ui\_persistence-\>date\_format [\#507](https://github.com/atk4/ui/issues/507)

**Merged pull requests:**

- feature/allow dash char in form input name [\#530](https://github.com/atk4/ui/pull/530) ([ibelar](https://github.com/ibelar))
- fix/\#522 Ajax error display [\#529](https://github.com/atk4/ui/pull/529) ([ibelar](https://github.com/ibelar))
- Fix/issue\#524 crud [\#527](https://github.com/atk4/ui/pull/527) ([ibelar](https://github.com/ibelar))
- Add FilterMode\TypeDatetime [\#520](https://github.com/atk4/ui/pull/520) ([skondakov](https://github.com/skondakov))
- Atk4/feature/add filter type boolean [\#519](https://github.com/atk4/ui/pull/519) ([skondakov](https://github.com/skondakov))

## 1.5.6

**Merged pull requests:**

- Fix decorator for boolean fields with enum set [\#533](https://github.com/atk4/ui/pull/533) ([DarkSide666](https://github.com/DarkSide666))
- fix/caughtException in tab [\#531](https://github.com/atk4/ui/pull/531) ([ibelar](https://github.com/ibelar))
- feature/Allow customization of container and table in grid. [\#528](https://github.com/atk4/ui/pull/528) ([ibelar](https://github.com/ibelar))

## 1.5.7

**Merged pull requests:**

- fix/FormSerializer option [\#537](https://github.com/atk4/ui/pull/537) ([ibelar](https://github.com/ibelar))

## 1.5.8

 - Modals now support "null" title. Will remove extra spacing for the header too
 - Modal content height is now 100px minimum
 - Added $app->isJsonRequest(), detects xmlhttprequest and tabs
 - Dynamic and Static tabs now have consistent padding
 - $tabs->addTab()->setActive() can be used to automatically jump to tab on load
 - When using stand-alone $view->render() support for callbacks improved
 - jsModal::setOption() can be used to customize header and label.

## 1.4

In this version we focus on high-level components: Wizard, Console, ProgressBar, AutoComplete field, Lister, Radio field
and necessary modifications to implement Login add-on.

For detailed explanation read this article: https://medium.com/@romaninsh/atk-newsletter-1-ui-1-4-released-cb7b84fc12c8.

**Implemented enhancements:**

- Modal::show\(\) arguments. [\#255](https://github.com/atk4/ui/issues/255)
- Implement session memorize [\#116](https://github.com/atk4/ui/issues/116)
- Feature/implement wizard [\#305](https://github.com/atk4/ui/pull/305) ([romaninsh](https://github.com/romaninsh))

**Fixed bugs:**

- Adding child and re-rendering has no effect [\#296](https://github.com/atk4/ui/issues/296)
- CRUD edit form is broken [\#295](https://github.com/atk4/ui/issues/295)
- form2 demo page is bad [\#290](https://github.com/atk4/ui/issues/290)
- Feature/fix 295 [\#297](https://github.com/atk4/ui/pull/297) ([romaninsh](https://github.com/romaninsh))
- Fix implementation of Radio field [\#292](https://github.com/atk4/ui/pull/292) ([romaninsh](https://github.com/romaninsh))

**Closed issues:**

- Label-\>link\(\) unneccessary [\#330](https://github.com/atk4/ui/issues/330)
- AutoComplete does not work if model id\_field != 'id' or title\_field' != name [\#327](https://github.com/atk4/ui/issues/327)
- Can not move Modal window by dragging of its title bar [\#323](https://github.com/atk4/ui/issues/323)
- Form error reloads in new popup window [\#318](https://github.com/atk4/ui/issues/318)
- Form does not handle integer fields set to 0 correctly [\#317](https://github.com/atk4/ui/issues/317)
- Paginator with self-reloading option is broken after change to CallbackLater [\#313](https://github.com/atk4/ui/issues/313)
- Implement Upload Field for form [\#304](https://github.com/atk4/ui/issues/304)
- even though $console-\>send\(\) is documented, it's not implemented. [\#302](https://github.com/atk4/ui/issues/302)
- CRUD values from model don't propagate to boolean and dropdown values [\#288](https://github.com/atk4/ui/issues/288)
- AutoComplete ignores model conditions until input is done [\#282](https://github.com/atk4/ui/issues/282)
- when URL omits `index.php` URL detection is working incorrectly. [\#279](https://github.com/atk4/ui/issues/279)
- Create example with header/footer row in table [\#276](https://github.com/atk4/ui/issues/276)
- \[Epic\] - Console [\#275](https://github.com/atk4/ui/issues/275)
- \[Epic\] - progress bar [\#274](https://github.com/atk4/ui/issues/274)
- \[Epic\] Wizard [\#273](https://github.com/atk4/ui/issues/273)
- Console and Progress-bar [\#160](https://github.com/atk4/ui/issues/160)
- TableColumn\Template not supporting HTML [\#135](https://github.com/atk4/ui/issues/135)
- Minor Improvements to jsReload [\#75](https://github.com/atk4/ui/issues/75)
- add lister documentation [\#27](https://github.com/atk4/ui/issues/27)
- integrate selenium testsuite [\#9](https://github.com/atk4/ui/issues/9)

**Merged pull requests:**

- fix jsCallback catch on Validation Exception [\#347](https://github.com/atk4/ui/pull/347) ([ibelar](https://github.com/ibelar))
- Doc/upload field [\#346](https://github.com/atk4/ui/pull/346) ([ibelar](https://github.com/ibelar))
- Feature/api redirect [\#345](https://github.com/atk4/ui/pull/345) ([romaninsh](https://github.com/romaninsh))
- Updating readme for 1.4 [\#344](https://github.com/atk4/ui/pull/344) ([romaninsh](https://github.com/romaninsh))
- fix tab callback [\#343](https://github.com/atk4/ui/pull/343) ([romaninsh](https://github.com/romaninsh))
- Add View::jsReload\(\) [\#341](https://github.com/atk4/ui/pull/341) ([romaninsh](https://github.com/romaninsh))
- it's better to call getter method not access property directly [\#340](https://github.com/atk4/ui/pull/340) ([DarkSide666](https://github.com/DarkSide666))
- implement progress bar [\#337](https://github.com/atk4/ui/pull/337) ([romaninsh](https://github.com/romaninsh))
- feature/Autocomplete-with-dropdown-settings [\#335](https://github.com/atk4/ui/pull/335) ([ibelar](https://github.com/ibelar))
- feature/Modal observes changes [\#334](https://github.com/atk4/ui/pull/334) ([ibelar](https://github.com/ibelar))
- Implement $view-\>url\(\) functionality as per \#307 [\#333](https://github.com/atk4/ui/pull/333) ([romaninsh](https://github.com/romaninsh))
- fix \#330 [\#332](https://github.com/atk4/ui/pull/332) ([DarkSide666](https://github.com/DarkSide666))
- feature/Error-Modal-Rev\(fix\#318\) [\#331](https://github.com/atk4/ui/pull/331) ([ibelar](https://github.com/ibelar))
- fix \#327: title\_field and id\_field [\#328](https://github.com/atk4/ui/pull/328) ([PhilippGrashoff](https://github.com/PhilippGrashoff))
- fix/\#318 Form error [\#326](https://github.com/atk4/ui/pull/326) ([ibelar](https://github.com/ibelar))
- Feature/js package [\#320](https://github.com/atk4/ui/pull/320) ([ibelar](https://github.com/ibelar))
- fix 317 [\#319](https://github.com/atk4/ui/pull/319) ([DarkSide666](https://github.com/DarkSide666))
- resolve \#313 [\#314](https://github.com/atk4/ui/pull/314) ([romaninsh](https://github.com/romaninsh))
- Fix Hidden type field label display [\#311](https://github.com/atk4/ui/pull/311) ([ibelar](https://github.com/ibelar))
- Feature/upload field [\#306](https://github.com/atk4/ui/pull/306) ([ibelar](https://github.com/ibelar))
- Feature/fix 302 [\#303](https://github.com/atk4/ui/pull/303) ([romaninsh](https://github.com/romaninsh))
- Inserted a link to calendar github page in comment [\#300](https://github.com/atk4/ui/pull/300) ([PhilippGrashoff](https://github.com/PhilippGrashoff))
- Removed empty first line [\#299](https://github.com/atk4/ui/pull/299) ([PhilippGrashoff](https://github.com/PhilippGrashoff))
- Added implementation of Lister [\#298](https://github.com/atk4/ui/pull/298) ([romaninsh](https://github.com/romaninsh))
- Move cloneRegion into lister's init [\#294](https://github.com/atk4/ui/pull/294) ([romaninsh](https://github.com/romaninsh))
- Feature/refactor region population [\#293](https://github.com/atk4/ui/pull/293) ([romaninsh](https://github.com/romaninsh))
- Enhancements of our Form [\#291](https://github.com/atk4/ui/pull/291) ([romaninsh](https://github.com/romaninsh))
- Corrected mistyped "Exception" \(was "Exceptino"\) [\#287](https://github.com/atk4/ui/pull/287) ([PhilippGrashoff](https://github.com/PhilippGrashoff))
- Fix/\#282-Autocomplete [\#285](https://github.com/atk4/ui/pull/285) ([ibelar](https://github.com/ibelar))
- Implementing Console [\#280](https://github.com/atk4/ui/pull/280) ([romaninsh](https://github.com/romaninsh))
- Improvements for Tables, CRUDS and few other areas. [\#277](https://github.com/atk4/ui/pull/277) ([romaninsh](https://github.com/romaninsh))

### 1.4.1

Added `$app->dbConnect()` as a simpler way to connect to the database. Some improvements in JS libraries.

**Merged pull requests:**

- Feature/add dbconnect [\#353](https://github.com/atk4/ui/pull/353) ([romaninsh](https://github.com/romaninsh))
- Feature/js package 1.0.1 [\#350](https://github.com/atk4/ui/pull/350) ([ibelar](https://github.com/ibelar))
- Fix/demo typo [\#348](https://github.com/atk4/ui/pull/348) ([ibelar](https://github.com/ibelar))

### 1.4.2

**Closed issues:**

- Add one more argument to jsModal\(\) an Modal\(\) for JS callback [\#364](https://github.com/atk4/ui/issues/364)

**Merged pull requests:**

- relpace QuickSearch with a JS by default [\#375](https://github.com/atk4/ui/pull/375) ([romaninsh](https://github.com/romaninsh))
- Cleaned up button demo [\#374](https://github.com/atk4/ui/pull/374) ([romaninsh](https://github.com/romaninsh))
- Feature/jsSearch-update [\#371](https://github.com/atk4/ui/pull/371) ([ibelar](https://github.com/ibelar))
- Fix/\#364 \#270 [\#368](https://github.com/atk4/ui/pull/368) ([ibelar](https://github.com/ibelar))
- Small cleanups [\#361](https://github.com/atk4/ui/pull/361) ([gartner](https://github.com/gartner))
- Fix/\#356 Upload Field Set [\#360](https://github.com/atk4/ui/pull/360) ([ibelar](https://github.com/ibelar))
- Fixed typo [\#359](https://github.com/atk4/ui/pull/359) ([PhilippGrashoff](https://github.com/PhilippGrashoff))

## 1.4.3


**Closed issues:**

- Bug in Table-\>setModel, should always return model object [\#390](https://github.com/atk4/ui/issues/390)
- Grid/Table can not handle $g-\>addColumn\(null, ..., null\) [\#388](https://github.com/atk4/ui/issues/388)
- Fix "method\_complexity" issue in src/FormLayout/Generic.php [\#383](https://github.com/atk4/ui/issues/383)
- masterCrud - add element , hit enter, no UI feedback but adds multiple items [\#379](https://github.com/atk4/ui/issues/379)
- Field hints  [\#377](https://github.com/atk4/ui/issues/377)
- app method url\(\) should have an argument indicating a regular or ajax url [\#369](https://github.com/atk4/ui/issues/369)
- Console don't show log records [\#367](https://github.com/atk4/ui/issues/367)
- CRUD does not work when placed inside Dynamic Tab [\#363](https://github.com/atk4/ui/issues/363)

**Merged pull requests:**

- fix \#390 and fix \#388 [\#391](https://github.com/atk4/ui/pull/391) ([DarkSide666](https://github.com/DarkSide666))
- includeJS -\> requireJS / includeCSS -\> requireCSS [\#386](https://github.com/atk4/ui/pull/386) ([gartner](https://github.com/gartner))
- Feature/jsUrl Rev [\#382](https://github.com/atk4/ui/pull/382) ([ibelar](https://github.com/ibelar))
- Update semantic UI to 2.3.0 and jQuery to 3.3.1 [\#381](https://github.com/atk4/ui/pull/381) ([romaninsh](https://github.com/romaninsh))
- Fix/\#379 [\#380](https://github.com/atk4/ui/pull/380) ([ibelar](https://github.com/ibelar))
- Feature/add form label support [\#378](https://github.com/atk4/ui/pull/378) ([romaninsh](https://github.com/romaninsh))
- Feature/various fixes [\#354](https://github.com/atk4/ui/pull/354) ([romaninsh](https://github.com/romaninsh))

## 1.4.4 

**Closed issues:**

- jsReload to allow a callback [\#387](https://github.com/atk4/ui/issues/387)

**Merged pull requests:**

- fix/\#387-Allow a js function to run after reload is complete. [\#395](https://github.com/atk4/ui/pull/395) ([ibelar](https://github.com/ibelar))
- Improvements to console, new demo and more documentation [\#394](https://github.com/atk4/ui/pull/394) ([romaninsh](https://github.com/romaninsh))

## 1.4.5


**Closed issues:**

- CRUD - add if model is loaded.. [\#362](https://github.com/atk4/ui/issues/362)
- If you don't set Layout of your App, then get useless exception [\#312](https://github.com/atk4/ui/issues/312)

**Merged pull requests:**

- fix/\#421-Alert [\#423](https://github.com/atk4/ui/pull/423) ([ibelar](https://github.com/ibelar))
- Feature/clean up docblocks [\#417](https://github.com/atk4/ui/pull/417) ([romaninsh](https://github.com/romaninsh))
- Feature/add card [\#416](https://github.com/atk4/ui/pull/416) ([romaninsh](https://github.com/romaninsh))
- Feature/Draggable [\#409](https://github.com/atk4/ui/pull/409) ([ibelar](https://github.com/ibelar))
- Implement "Multiformat" column decorator for table [\#408](https://github.com/atk4/ui/pull/408) ([romaninsh](https://github.com/romaninsh))
- feature/Draggable [\#407](https://github.com/atk4/ui/pull/407) ([ibelar](https://github.com/ibelar))
- Make use of getTitle\(\) and getModelCaption\(\) [\#406](https://github.com/atk4/ui/pull/406) ([romaninsh](https://github.com/romaninsh))
- Autocomplete to use $title\_field instead of 'name' [\#403](https://github.com/atk4/ui/pull/403) ([FabulousGee](https://github.com/FabulousGee))
- Fix: Typo [\#401](https://github.com/atk4/ui/pull/401) ([gartner](https://github.com/gartner))
- Removed "return" in demo which was blocking some examples. [\#399](https://github.com/atk4/ui/pull/399) ([FabulousGee](https://github.com/FabulousGee))
- Cleanup of Pr/397: Added getField-method to Generic FormLayout [\#398](https://github.com/atk4/ui/pull/398) ([romaninsh](https://github.com/romaninsh))

## 1.3

This version is focused on dynamic interaction between the browser and your PHP apps. It contains 3
new Components and 3 new Actions and a new Form Field Decorator.

Loader (#246, #250) is a component that calls itself back to load its content. While the content is being generated,
your user will see a spinner animation:

``` php
$loader = $app->add('Loader');

$loader->set(function($p) {
    sleep(2);  // or any other slow-loading code.
    $p->add('LoremIpsum');
});
```

There are also ways to trigger and reload the contents as well as passing some arguments in. We include 2
demos for the loader: [basic loader demo](http://ui.agiletoolkit.org/demos/loader.php) and
[practical use example](http://ui.agiletoolkit.org/demos/loader2.php). For additional information,
look into [Loader Documentation](http://agile-ui.readthedocs.io/en/latest/virtualpage.html?highlight=loader#loader)

Next we thought - why not also load content [dynamically inside a Modal dialog](http://ui.agiletoolkit.org/demos/modal2.php), so we added this:

``` php
$modal = $app->add(['Modal', 'title' => 'Lorem Ipsum load dynamically']);

$modal->set(function ($p) {
    sleep(2);  // or any other slow-loading code.
    $p->add('LoremIpsum');
});
```

Code is very consistent with the Loader or dynamic definition of
[Tabs](http://agile-ui.readthedocs.io/en/latest/tabs.html?highlight=tabs#dynamic-tabs)
but would open in a modal window. However we wanted to go even further.

What if it would take several seconds for content to load? We used Server-Sent-Events
for streaming updates from your PHP code in real-time (#258 #259). Yet it's just as simple
to use as anything else in Agile UI:

``` php
// see SSE demo for $bar implementation

$button->on('click', $sse->set(function () use ($sse, $bar) {

    sleep(0.5);
    $sse->send($bar->js()->progress(['percent' => 40]));
    sleep(2);
    $sse->send($bar->js()->progress(['percent' => 80]));
    sleep(1);
    return $bar->js()->progress(['percent' => 100]);
}));
```

In the next release we will include 'ProgressBar' and 'Console' classes that rely
on event streaming.

Other additions include:

 - [AutoComplete field](http://ui.agiletoolkit.org/demos/autocomplete.php) for dynamically loading contents of a drop-down. #245
 - [Notifyer](http://ui.agiletoolkit.org/demos/notify.php) for flashing success or error messages on top of the screen dynamically. #242
 - [jsModal](http://ui.agiletoolkit.org/demos/modal2.php) Action for opening Modal windows

We also added AutoComplete "Plus" mode. It's a button next to your AutoComplete field which you can click to add new element
inside a referenced entity which will then be automatically filled-in. Super-useful!

Lastly - a lot of new documentation and minor fixes. #240 #244 #248 #256 #257

Our Test-suite now includes broser testing. #262 #263

### 1.3.1

Fixed bug in dependencies, which was requesting behat/mink-zombie-driver as a dependency.

### 1.3.2

Fixed issues related to PHP 7.2

 - Template.php uses depreciated each(). #278
 - using url() on index.php not working. #279
 - make url() more flexible (for 3rd party integrations), #271


## 1.2

This release includes change to view constructor arguments, huge JavaScript overhaul and clean-up,
refactored jsModal implementation, refactor of Table::addColumn() and Table::addField(), integration
with Wordpress and a lot of new documentation.

This release was possible thanks to our new contributors:
 - [ibelar](https://github.com/ibelar)
 - [TheGrammerNazi](https://github.com/TheGrammerNazi)
 - [Darkside666](https://github.com/Darkside666)

### Major Changes
 - Refactored View arguments. `$button = new Button($label, $class)` instead of using arrays. Backwards compatible.
 - Migrated to [Agile Core 1.3](https://github.com/atk4/core/releases/tag/1.3.0) and [Agile Data 1.2.1](https://github.com/atk4/data/releases/tag/1.2.1)
 - Added support for Tabs 
 - Added notify.js #205
 - Add Callback::triggered() method.  #226
 - Refactored JS Plugin System. ATK now implements: #189, #201, #193, #202
   - spinner (link to doc needed)
   - reloadView (link to doc needed)
   - ajaxec (link to doc needed)
   - createModal (link to doc needed)
 - Refactored addField() and addColumn() #179 #187 #223 #225

### Other changes 
 - Documentation improvements:
   - Callbacks and Virtual pages #200 (http://agile-ui.readthedocs.io/en/latest/core.html#callbacks-and-virtual-pages)
   - README file #196
   - Add documentation for icon, image, label, loremipsum, message, tablecolumn, text, decorators. #218
 - Fixed problem with CheckBox on a form #130
 - Fixed form submission with Enter #173
 - Improved form validation #191
 - Fix label display when it's 0 #198
 - Cleanups #207 #218
 - Switched to codecov.io for a more serious coverage reports (will focus on improving those)

### Minor releases (in reverse order)

#### 1.2.1

 - fixed warning in PHP 5.6.x for `function(string $name)`.

#### 1.2.2

Bugfixes #231 #232 #230 #229 #235

 - Virtual Page cannot have multiple callbacks
 - Grid::addDecorator added to proxy Table::addDecorator
 - Field type "boolean" now properly shown on grid/crud (thanks @slava-vishnyakov)
 - Virtual Page trigger allows [$obj, 'methd'] callables now
 - Added more tests

#### 1.2.3

Bugfixes #234, #238, #239

 - Reloader now properly shows error in a popup, if exception occurs.

## 1.1

A massive release containing unimaginable amount of new features, fixes and actually the first version
of Agile Data that allows you to actually build nice apps with it.

### Major Features
- Added CRUD component to add, edit, delete and add records to your data sets #105, 
- Added Advanced Grid now supporting checkbox column, actions, multiple formatters, anonymous columns, and delete
- .. also Renamed old Grid into Table, #118 #84 #83 #93 #95 #64
- Added QuickSearch #107 
- Added Paginator
- Added Form Model, Validation support
- Added Form Fields: TextArea, DropDown
- Added Automated multi-column layout FormLayout\Columns
- Added support for stickyGet #131
- Added jsModal() for dialogs #124 #71
- Added jsReload() with argument support and spinner #51 #66 #78 #79 
- Added Message #100
- Added Label #88
- Added Columns #65
- Added JS Library #73
- Form can edit all field types of from Agile Data
- Renamed Grid into Table

### New Demo Pages
- Layouts #123 #113
- Form / Multi-column layout
- Grid / Table Interactions
- Grid / Table+Bar+Search+Paginator
- Grid / Interactivity - Modals
- Crud
- View demo #104
- Message
- Labels
- Menu #96 #97 
- Paginator
- Interactivity / Element Reloading
- Interactivity / Modal Dialogs
- Interactivity / Sticky GET
- Interactivity / Recursive Views

### Fixes
- Bugfixes #111, #86, #85

### Minor changes
- Upgraded to Agile Core 1.2 #129
- Field->jsInput()
- App->requireJS() #120 #50
- Remaned all .jade files into .pug #89
- Renamed namespace Column into TableColumn

Full diff: https://github.com/atk4/ui/compare/1.0.3...1.1.0

### Minor releases (in reverse order)

#### 1.1.1

- Use proper CDN for 3rd party CSS/JS code #150
- Add support for 'password' type #143
- Fix bad error with addColumn() when using non-existant field #134
- Option for Money Table Column to hide zero values #152
- Fix reloading bug #149
- Improve exit; support in callbacks #151
- Other bugfixes #133

#### 1.1.2

- Implemented Grid / Table sorting #163
- CRUD look and feel improvements #156
- Added support for passing arguments into on('', function($arg)) from JS
- Bugfixes #164

#### 1.1.3

- Improve UI layout and add responsivitiy #170
- Documentation restructure, new Overview section, many more screenshots #154
- Added support for multiple formatters in Table. You can use 'addColumn' with existing column. #162
- Added type 'text', improve how 'money', date and time appear on a form. #165
- Improve the way hasOne relations are displayed on the form #165 (dropdowns)
- Fix linking to JS libraries in the CDN
- Bugfixes in Menu
- Renamed `$layout->leftMenu` into `$layout->menuLeft` to follow principle of "Left/Right" always being last word.

#### 1.1.4

- Improve CDN handling. Using `$app->cdn = false` will disable it.

#### 1.1.5 - 1.1.9 (multiple releases)

Probably the last big release before 1.2.x

 - Added new Form Validation implementation #177
 - Table totals can now include min, max and count #178
 - Refactored asset includes (can now be cached locally) #181
 - Footer now indicates version

#### 1.1.10

 - Fix warning in database demos
 - Fix detection of local public files for demos
 - Fix Delete button in crud (couldn't be clicked twice)
 - Enabled App to have dynamic methods
 - Fixed bug in Status column
 - Fixed stickyURL #185
 - Improved compatibility with custom JS renderers (for wordpress integration)
 - Fixed centered layout #186
 - "get-assets.php" now creates 'public' folder, usable in your project

## 1.0 Release

* Implement Grid
* Many improvements everywhere
* Simpler execution
* First stable release

### Minor Releases

#### 1.0.2

* Button::$rightIcon renamed into $iconRight to follow global pattern
* Removed depreciated classes H2, Fields and MiniApp
* Cleaned up demos/button.php
* Added documentation for Button class
* Refactored Button internals (simplified), now uses button.html
* Added comments for a Form
* Cleaned up Grid type-hinting
* Added example for top/bottom attached buttons to Grid.
* You can disable "header" for grid now

#### 1.0.1

Qucik post-release bugfixes

#### 1.0.0

## Pre-Releases

### 0.4

* Implemented Layouts (Admin / Centered) #33
* Created nicer demos

### 0.3

* Implemented js() and on() #20
* Implemented Server-Side JS calls #28
* Implemented Form #29 and #30
* Enhanced documentation

### 0.2

* Implemented Render Tree
* Implemented Template-based Rendering #15
* Implemented Basic View #16
* Implemented Button (based around Semantic UI)
* Implemented JavaScript events
* Advanced JSChains (enclosing, etc) #18
* Implemented Very Basic Layouts

### 0.1

* Initial Release
* Bootstraped Documentation (sphinx-doc)
* Implemented CI


\* *This Change Log was automatically generated by [github_changelog_generator](https://github.com/skywinder/Github-Changelog-Generator)*
