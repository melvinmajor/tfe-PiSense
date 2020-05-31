<?php

require 'init.php';

/**
 * Example implementation of a dynamic view which support session.
 *
 * Cart will memorize and restore its items into session. Cart will also
 * render the items.
 */
class Cart extends \atk4\ui\Lister
{
    use \atk4\core\SessionTrait;

    public $items = [];

    public $defaultTemplate = 'lister.html';

    public function init()
    {
        parent::init();
        $this->items = $this->recall('items', []);

        // Clicking on any URL produced by this Lister will carry on an extra GET argument
        $this->stickyGet($this->name.'_remove', true);

        // Set default description for our row template. Normally this is replaced by the 'descr' field
        // of a model, but we don't have it, so it will stay like this.
        $this->t_row['descr'] = 'click on link to remove item';

        // We link to ourselves with this special GET argument to indicate that item must be removed.
        if (isset($_GET[$this->name.'_remove'])) {
            $this->removeItem($_GET['id']);

            // redirect again, since we don't want this to stay in the URL
            $this->app->redirect([$this->name.'_remove'=>false]);
        }
    }

    /**
     * adding an item into the cart.
     */
    public function addItem($item)
    {
        $this->items[] = $item;
        $this->memorize('items', $this->items);
    }

    /**
     * remove item form the cart with specified index.
     */
    public function removeItem($no)
    {
        unset($this->items[$no]);
        $this->memorize('items', $this->items);
    }

    /**
     * renders as a regular lister, but source is the items.
     */
    public function renderView()
    {
        // memorize items

        $this->setSource($this->items);

        return parent::renderView();
    }
}

/**
 * Implementation of a generic item shelf. Shows selection of products and allow to bind click event.
 *
 * Method linkCart allow you to link ItemShelf with Cart. Clicking on a shelf item will place that
 * item inside a cart reloading it afterwards.
 */
class ItemShelf extends \atk4\ui\View
{
    public $ui = 'green segment';

    public function init()
    {
        parent::init();
        $v = $this->add('View', ['ui'=>'fluid']);
        $cols = $v->add('Columns', ['ui' => 'relaxed divided grid']);

        $c1 = $cols->addColumn();
        $c1->add('Header', ['size' => 'small'])->set('Snacks');
        $l1 = $c1->add('View', ['ui' => 'list']);
        $l1->add('Item', ['content'=>'Crisps', 'ui' => 'item'])->setElement('a');
        $l1->add('Item', ['content'=>'Pork Scratchings', 'ui' => 'item'])->setElement('a');
        $l1->add('Item', ['content'=>'Candies', 'ui' => 'item'])->setElement('a');
        $l1->add('Item', ['content'=>'Sweets', 'ui' => 'item'])->setElement('a');

        $c2 = $cols->addColumn();
        $c2->add('Header', ['size' => 'small'])->set('Drinks');
        $l2 = $c2->add('View', ['ui' => 'list']);
        $l2->add('Item', ['content'=>'Fizzy Drink', 'ui' => 'item'])->setElement('a');
        $l2->add('Item', ['content'=>'Hot Latte', 'ui' => 'item'])->setElement('a');
        $l2->add('Item', ['content'=>'Water', 'ui' => 'item'])->setElement('a');
        $l2->add('Item', ['content'=>'Apple Juice', 'ui' => 'item'])->setElement('a');

        $c3 = $cols->addColumn();
        $c3->add('Header', ['size' => 'small'])->set('Mains');
        $l3 = $c3->add('View', ['ui' => 'list']);
        $l3->add('Item', ['content'=>'Chicken Tikka', 'ui' => 'item'])->setElement('a');
        $l3->add('Item', ['content'=>'Green Curry', 'ui' => 'item'])->setElement('a');
        $l3->add('Item', ['content'=>'Pastries', 'ui' => 'item'])->setElement('a');
    }

    /**
     * Associate your shelf with cart, so that when item is clicked, the content of a
     * cart is updated.
     *
     * Also - you can supply jsAction to execute when this happens.
     */
    public function linkCart(Cart $cart, $jsAction = null)
    {
        $this->on('click', '.item', function ($a, $b) use ($cart, $jsAction) {
            $cart->addItem($b);

            return $jsAction;
        }, [(new \atk4\ui\jQuery())->text()]);
    }
}

$app->add('Header')->set('Menu popup');
$m = $app->add('Menu');

// You may add popup on top of menu items or dropdowns. Dropdowns have a slightly different
// look, with that triangle on the right. You don't have to add pop-up right away, it can be
// added later.
$browse = $m->add(['DropDown', 'Browse']);

// Add cart item into the menu, with a popup inside
$cart_item = $m->addItem(['Cart', 'icon'=>'cart']);

$cart_popup = $app->add(['Popup', $cart_item, 'position'=>'bottom left']);
// Popup won't dissapear as you hover over it.
$cart_popup->setHoverable();

$shelf = $app->add(new ItemShelf());

// Here we are facing a pretty interesting problem. If you attempt to put "Cart" object inside a popup directly,
// it won't work, because it will be located inside the menu item's DOM tree and, although hidden, will be
// impacted by some css rules of the menu.
//
// This can happen when your popup content is non-trivial. So we are moving Popup into the app and linking up
// the triggers. Now, since it's outside, we can't use a single jsAction to reload menu item (along with label)
// and the contens. We could use 2 requests for reloading, but that's not good.
//
// The next idea is to make cart dynamic, so it loads when you move mouse over the menu. This probably is good,
// as it will always be accurate, even if you added items form multiple browser tabs.
//
// However in this case Cart object will exist only inside the popup callback, and we won't be able to get
// the label out.
//
// The final solution works like this - Cart is added to the application directly at first. It's initialized
// as i would be in the application. That's also impacts under which key 'memorize' is storing data - having
// two different objects won't work, since they won't share session data.

$cart = $app->add(new Cart());

// Next I am calling destroy. This won't actually destroy the cart, but it will remove it from the application.
// If i add unset($cart) afterwards, garbage collector will trigger destructor. Instead I'm passing $cart
// into the callback and making it part of the pop-up render tree.
$cart->destroy();

// Label now can be added referencing Cart's items. Init() was colled when I added it into app, so the
// item property is populated.
$cart_outter_label = $cart_item->add(['Label', count($cart->items), 'floating red ']);
if (!$cart->items) {
    $cart_outter_label->addStyle('display', 'none');
}

$cart_popup->set(function ($popup) use ($shelf, $cart_outter_label, $cart) {
    $cart_inner_label = $popup->add(['Label', 'Number of items:']);

    // cart is already initialized, so init() is not called again. However, cart will be rendered
    // as a child of a pop-up now.
    $cart = $popup->add($cart);

    $cart_inner_label->detail = count($cart->items);
    $popup->add('Item')->setElement('hr');
    $btn = $popup->add(['Button', 'Checkout', 'primary small']);
});

// Add item shelf below menu and link it with the cart
$shelf->linkCart($cart, [
    // array is a valid js action. Will relad cart item (along with drop-down and label)
    $cart_outter_label->jsReload(),

    // also will hide current item from the shelf
    (new \atk4\ui\jQuery())->hide(),
]);

// label placed on top of menu item, not in the popup

$pop = $app->add(['Popup', $browse, 'position' => 'bottom left', 'minWidth'=>'500px'])
           ->setHoverable()
           ->setOption('delay', ['show' => 100, 'hide' => 400]);
$shelf2 = $pop->add(new ItemShelf());
//$shelf2->linkCart($cart, $cart_item->jsReload());

//////////////////////////////////////////////////////////////////////////////

$um = $m->add(['Menu', 'ui' => false], 'RightMenu')
    ->addClass('right menu')->removeClass('item');
$m_right = $um->addMenu(['', 'icon'=>'user']);

// If you add popup right inside the view, it will link itself with the element. If you are adding it into other container,
// you can still manually link it and specify an event.
$signup = $app->add(['Popup', $m_right, 'position' => 'bottom right'])->setHoverable();

// This popup will be dynamically loaded.
$signup->stickyGet('logged');
$signup->set(function ($pop) {

    // contetn of the popup will be different depending on this condition.
    if (isset($_GET['logged'])) {
        $pop->add(['Message', 'You are already logged in as '.$_GET['logged']]);
        $pop->add(['Button', 'Logout', 'primary', 'icon'=>'sign out'])
            ->link($pop->app->url());
    } else {
        $f = $pop->add('Form');
        $f->addField('email', null, ['required'=>true]);
        $f->addField('password', ['Password'], ['required'=>true]);
        $f->buttonSave->set('Login');

        // popup handles callbacks properly, so dynamic element such as form works
        // perfectly inside a popup.
        $f->onSubmit(function ($f) {
            if ($f->model['password'] != '123') {
                return $f->error('password', 'Please use password "123"');
            }

            // refreshes entire page
            return $f->app->jsRedirect(['logged'=>$f->model['email']]);
            //return new atk4\ui\jsExpression('alert([])', ['Thank you '.$f->model['email']]);
        });
    }
});

//////////////////////////////////////////////////////////////////////////////

$app->add('Header')->set('Specifying trigger');

$button = $app->add(['Button', 'Click Me', 'primary']);

$b_pop = $app->add(['Popup', $button]);

$b_pop->add('Header')->set('Using click events');
$b_pop->add('View')->set('Adding popup into button activates on click by default. Clicked popups will close if you click away.');

$input = $app->add(new \atk4\ui\FormField\Line(['placeholder' => 'Search users', 'icon' => 'circular search link']));

$i_pop = $app->add(['Popup', $input, 'triggerOn' => 'focus']);
$i_pop->add('View')->set('You can use this field to search data.');

$button = $app->add(['Button', null, 'icon'=>'volume down']);
$b_pop = $app->add(['Popup', $button, 'triggerOn'=>'hover'])->setHoverable();

$b_pop->add(['FormField\CheckBox', 'Just On/Off', 'slider'])->on('change', $button->js()->find('.icon')->toggleClass('up down'));
