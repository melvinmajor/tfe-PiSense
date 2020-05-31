<?php

namespace atk4\ui;

class App
{
    use \atk4\core\InitializerTrait {
        init as _init;
    }

    use \atk4\core\HookTrait;
    use \atk4\core\DynamicMethodTrait;
    use \atk4\core\FactoryTrait;
    use \atk4\core\AppScopeTrait;
    use \atk4\core\DIContainerTrait;

    // @var array|false Location where to load JS/CSS files
    public $cdn = [
        'atk'              => 'https://cdn.jsdelivr.net/gh/atk4/ui@1.7.1/public',
        'jquery'           => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1',
        'serialize-object' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery-serialize-object/2.5.0',
        'semantic-ui'      => 'https://cdn.jsdelivr.net/npm/fomantic-ui@2.7.2/dist',
    ];

    // @var string Version of Agile UI
    public $version = '1.7.1';

    // @var string Name of application
    public $title = 'Agile UI - Untitled Application';

    // @var Layout\Generic
    public $layout = null; // the top-most view object

    /**
     * Set one or more directories where templates should reside.
     *
     * @var string|array
     */
    public $template_dir = null;

    // @var string Name of skin
    public $skin = 'semantic-ui';

    /**
     * Will replace an exception handler with our own, that will output errors nicely.
     *
     * @var bool
     */
    public $catch_exceptions = true;

    /**
     * Will display error if callback wasn't triggered.
     *
     * @var bool
     */
    public $catch_runaway_callbacks = true;

    /**
     * Will always run application even if developer didn't explicitly executed run();.
     *
     * @var bool
     */
    public $always_run = true;

    /**
     * Will be set to true after app->run() is called, which may be done automatically
     * on exit.
     *
     * @var bool
     */
    public $run_called = false;

    /**
     * Will be set to true, when exit is called. Sometimes exit is intercepted by shutdown
     * handler and we don't want to execute 'beforeExit' multiple times.
     *
     * @var bool
     */
    public $exit_called = false;

    // @var bool
    public $_cwd_restore = true;

    /**
     * function setModel(MyModel $m);.
     *
     * is considered 'WARNING' even though MyModel descends from the parent class. This
     * is not an incompatible class. We want to write clean PHP code and therefore this
     * warning is disabled by default until it's fixed correctly in PHP.
     *
     * See: http://stackoverflow.com/a/42840762/204819
     *
     * @var bool
     */
    public $fix_incompatible = true;

    // @var bool
    public $is_rendering = false;

    // @var Persistence\UI
    public $ui_persistence = null;

    /**
     * @var View For internal use
     */
    public $html = null;

    /**
     * @var LoggerInterface, target for objects with DebugTrait
     */
    public $logger = null;

    // @var \atk4\data\Persistence
    public $db = null;

    /**
     * @var bool Whether or not semantic-ui vue has been initialised.
     */
    private $is_sui_init = false;

    /**
     * @var string used in method App::url to build the url
     *
     * Used only in method App::url
     * Remove and re-add the extension of the file during parsing requests and building urls
     */
    protected $url_building_ext = '.php';

    /**
     * Constructor.
     *
     * @param array $defaults
     */
    public function __construct($defaults = [])
    {
        $this->app = $this;

        // Process defaults
        if (is_string($defaults)) {
            $defaults = ['title' => $defaults];
        }

        if (isset($defaults[0])) {
            $defaults['title'] = $defaults[0];
            unset($defaults[0]);
        }

        /*
        if (is_array($defaults)) {
            throw new Exception(['Constructor requires array argument', 'arg' => $defaults]);
        }*/
        $this->setDefaults($defaults);
        /*

        foreach ($defaults as $key => $val) {
            if (is_array($val)) {
                $this->$key = array_merge(isset($this->$key) && is_array($this->$key) ? $this->$key : [], $val);
            } elseif (!is_null($val)) {
                $this->$key = $val;
            }
        }
         */

        // Set up template folder
        if ($this->template_dir === null) {
            $this->template_dir = [];
        } elseif (!is_array($this->template_dir)) {
            $this->template_dir = [$this->template_dir];
        }
        $this->template_dir[] = __DIR__.'/../template/'.$this->skin;

        // Set our exception handler
        if ($this->catch_exceptions) {
            set_exception_handler(function ($exception) {
                return $this->caughtException($exception);
            });
        }

        if (!$this->_initialized) {
            //$this->init();
        }

        if ($this->fix_incompatible) {
            // PHP 7.0 introduces strict checks for method patterns. But only 7.2 introduced parameter type widening
            //
            // https://en.wikipedia.org/wiki/Covariance_and_contravariance_(computer_science)#Contravariant_method_argument_type
            // https://wiki.php.net/rfc/parameter-no-type-variance
            //
            // We wish to start using type-hinting more in our classes, but it would break any extends in 3rd party code unless
            // they are on 7.2.
            if (version_compare(PHP_VERSION, '7.0.0') >= 0 && version_compare(PHP_VERSION, '7.2.0') < 0) {
                set_error_handler(function ($errno, $errstr) {
                    return strpos($errstr, 'Declaration of') === 0;
                }, E_WARNING);
            }
        }

        // Always run app on shutdown
        if ($this->always_run) {
            if ($this->_cwd_restore) {
                $this->_cwd_restore = getcwd();
            }

            register_shutdown_function(function () {
                if (is_string($this->_cwd_restore)) {
                    chdir($this->_cwd_restore);
                }

                if (!$this->run_called) {
                    try {
                        $this->run();
                    } catch (\Exception $e) {
                        $this->caughtException($e);
                    }
                }

                $this->callExit();
            });
        }

        // Set up UI persistence
        if (!isset($this->ui_persistence)) {
            $this->ui_persistence = new Persistence\UI();
        }
    }

    public function callExit()
    {
        if (!$this->exit_called) {
            $this->exit_called = true;
            $this->hook('beforeExit');
        }
        exit;
    }

    /**
     * Catch exception.
     *
     * @param mixed $exception
     */
    public function caughtException($exception)
    {
        $this->catch_runaway_callbacks = false;

        $l = new \atk4\ui\App();
        $l->initLayout('Centered');

        //check for error type.
        if ($exception instanceof \atk4\core\Exception) {
            $l->layout->template->setHTML('Content', $exception->getHTML());
        } elseif ($exception instanceof \Error) {
            $l->layout->add(['Message', get_class($exception).': '.$exception->getMessage().' (in '.$exception->getFile().':'.$exception->getLine().')', 'error']);
            $l->layout->add(['Text', nl2br($exception->getTraceAsString())]);
        } else {
            $l->layout->add(['Message', get_class($exception).': '.$exception->getMessage(), 'error']);
        }
        $l->layout->template->tryDel('Header');

        if ($this->isJsonRequest()) {
            echo json_encode(['success'   => false,
                                'message' => $l->layout->getHtml(),
                             ]);
        } else {
            $l->catch_runaway_callbacks = false;
            $l->run();
            $this->run_called = true;
        }
        $this->callExit();
    }

    /**
     * Most of the ajax request will require sending exception in json
     * instead of html, except for tab.
     *
     * @return bool
     */
    protected function isJsonRequest()
    {
        $ajax = false;

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
           && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $ajax = true;
        }

        return $ajax && !isset($_GET['__atk_tab']);
    }

    /**
     * Outputs debug info.
     *
     * @param string $str
     */
    public function outputDebug($str)
    {
        echo 'DEBUG:'.$str.'<br/>';
    }

    /**
     * Will perform a preemptive output and terminate. Do not use this
     * directly, instead call it form Callback, jsCallback or similar
     * other classes.
     *
     * @param string $output
     */
    public function terminate($output = null)
    {
        if ($output !== null) {
            echo $output;
        }
        $this->run_called = true; // prevent shutdown function from triggering.
        $this->callExit();
    }

    /**
     * Initializes layout.
     *
     * @param string|Layout\Generic|array $seed
     *
     * @return $this
     */
    public function initLayout($seed)
    {
        $layout = $this->factory($seed, null, 'Layout');
        $layout->app = $this;

        if (!$this->html) {
            $this->html = new View(['defaultTemplate' => 'html.html']);
            $this->html->app = $this;
            $this->html->init();
        }

        $this->layout = $this->html->add($layout);

        $this->initIncludes();

        return $this;
    }

    /**
     * Initialize JS and CSS includes.
     */
    public function initIncludes()
    {
        // jQuery
        $url = isset($this->cdn['jquery']) ? $this->cdn['jquery'] : '../public';
        $this->requireJS($url.'/jquery.min.js');

        // Semantic UI
        $url = isset($this->cdn['semantic-ui']) ? $this->cdn['semantic-ui'] : '../public';
        $this->requireJS($url.'/semantic.min.js');
        $this->requireCSS($url.'/semantic.min.css');

        // Serialize Object
        $url = isset($this->cdn['serialize-object']) ? $this->cdn['serialize-object'] : '../public';
        $this->requireJS($url.'/jquery.serialize-object.min.js');

        // Agile UI
        $url = isset($this->cdn['atk']) ? $this->cdn['atk'] : '../public';
        $this->requireJS($url.'/atkjs-ui.min.js');
        $this->requireCSS($url.'/agileui.css');
    }

    /**
     * Adds a <style> block to the HTML Header. Not escaped. Try to avoid
     * and use file include instead.
     *
     * @param string $style CSS rules, like ".foo { background: red }".
     */
    public function addStyle($style)
    {
        if (!$this->html) {
            throw new Exception(['App does not know how to add style']);
        }
        $this->html->template->appendHTML('HEAD', $this->getTag('style', $style));
    }

    /**
     * Normalizes class name.
     *
     * @param string $name
     *
     * @return string
     */
    public function normalizeClassNameApp($name)
    {
        return '\\'.__NAMESPACE__.'\\'.$name;
    }

    /**
     * Add a new object into the app. You will need to have Layout first.
     *
     * @param mixed  $seed   New object to add
     * @param string $region
     *
     * @return object
     */
    public function add($seed, $region = null)
    {
        if (!$this->layout) {
            throw new Exception(['If you use $app->add() you should first call $app->setLayout()']);
        }

        return $this->layout->add($seed, $region);
    }

    /**
     * Runs app and echo rendered template.
     */
    public function run()
    {
        $this->run_called = true;
        $this->hook('beforeRender');
        $this->is_rendering = true;

        // if no App layout set
        if (!isset($this->html)) {
            throw new Exception(['App layout should be set.']);
        }

        $this->html->template->set('title', $this->title);
        $this->html->renderAll();
        $this->html->template->appendHTML('HEAD', $this->html->getJS());
        $this->is_rendering = false;
        $this->hook('beforeOutput');

        if (isset($_GET['__atk_callback']) && $this->catch_runaway_callbacks) {
            $this->terminate('!! Callback requested, but never reached. You may be missing some arguments in '.$_SERVER['REQUEST_URI']);
        }

        echo $this->html->template->render();
    }

    /**
     * Initialize app.
     */
    public function init()
    {
        $this->_init();
    }

    /**
     * Load template by template file name.
     *
     * @param string $name
     *
     * @throws Exception
     *
     * @return Template
     */
    public function loadTemplate($name)
    {
        $template = new Template();
        $template->app = $this;

        if (in_array($name[0], ['.', '/', '\\']) || strpos($name, ':\\') !== false) {
            return $template->load($name);
        } else {
            $dir = is_array($this->template_dir) ? $this->template_dir : [$this->template_dir];
            foreach ($dir as $td) {
                if ($t = $template->tryLoad($td.'/'.$name)) {
                    return $t;
                }
            }
        }

        throw new Exception(['Can not find template file', 'name'=>$name, 'template_dir'=>$this->template_dir]);
    }

    /**
     * Connects database.
     *
     * @param string $dsn      Format as PDO DSN or use "mysql://user:pass@host/db;option=blah", leaving user and password arguments = null
     * @param string $user
     * @param string $password
     * @param array  $args
     *
     * @throws \atk4\data\Exception
     * @throws \atk4\dsql\Exception
     *
     * @return \atk4\data\Persistence
     */
    public function dbConnect($dsn, $user = null, $password = null, $args = [])
    {
        return $this->db = $this->add(\atk4\data\Persistence::connect($dsn, $user, $password, $args));
    }

    protected function getRequestURI()
    {
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // IIS
            $request_uri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) { // Apache
            $request_uri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
            $request_uri = $_SERVER['ORIG_PATH_INFO'];
        // This one comes without QUERY string
        } else {
            $request_uri = '';
        }
        $request_uri = explode('?', $request_uri, 2);

        return $request_uri[0];
    }

    /**
     * @var null
     */
    public $page = null;

    /**
     * Build a URL that application can use for js call-backs. Some framework integration will use a different routing
     * mechanism for NON-HTML response.
     *
     * @param array|string $page           URL as string or array with page name as first element and other GET arguments
     * @param bool         $needRequestUri Simply return $_SERVER['REQUEST_URI'] if needed
     * @param array        $extra_args     Additional URL arguments
     *
     * @return string
     */
    public function jsURL($page = [], $needRequestUri = false, $extra_args = [])
    {
        return $this->url($page, $needRequestUri, $extra_args);
    }

    /**
     * Build a URL that application can use for loading HTML data.
     *
     * @param array|string $page           URL as string or array with page name as first element and other GET arguments
     * @param bool         $needRequestUri Simply return $_SERVER['REQUEST_URI'] if needed
     * @param array        $extra_args     Additional URL arguments
     *
     * @return string
     */
    public function url($page = [], $needRequestUri = false, $extra_args = [])
    {
        if ($needRequestUri) {
            return $_SERVER['REQUEST_URI'];
        }

        $sticky = $this->sticky_get_arguments;
        $result = $extra_args;

        if ($this->page === null) {
            $uri = $this->getRequestURI();

            if (substr($uri, -1, 1) == '/') {
                $this->page = 'index';
            } else {
                $this->page = basename($uri, $this->url_building_ext);
            }
        }

        // if page passed as string, then simply use it
        if (is_string($page)) {
            return $page;
        }

        // use current page by default
        if (!isset($page[0])) {
            $page[0] = $this->page;
        }

        //add sticky arguments
        if (is_array($sticky) && !empty($sticky)) {
            foreach ($sticky as $key => $val) {
                if ($val === true) {
                    if (isset($_GET[$key])) {
                        $val = $_GET[$key];
                    } else {
                        continue;
                    }
                }
                if (!isset($result[$key])) {
                    $result[$key] = $val;
                }
            }
        }

        // add arguments
        foreach ($page as $arg => $val) {
            if ($arg === 0) {
                continue;
            }

            if ($val === null || $val === false) {
                unset($result[$arg]);
            } else {
                $result[$arg] = $val;
            }
        }

        // put URL together
        $args = http_build_query($result);
        $url = ($page[0] ? $page[0].$this->url_building_ext : '').($args ? '?'.$args : '');

        return $url;
    }

    /**
     * Make current get argument with specified name automatically appended to all generated URLs.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function stickyGet($name)
    {
        if (isset($_GET[$name])) {
            $this->sticky_get_arguments[$name] = $_GET[$name];

            return $_GET[$name];
        }
    }

    /**
     * @var array global sticky arguments
     */
    protected $sticky_get_arguments = [];

    /**
     * Remove sticky GET which was set by stickyGET.
     *
     * @param string $name
     */
    public function stickyForget($name)
    {
        unset($this->sticky_get_arguments[$name]);
    }

    /**
     * Adds additional JS script include in aplication template.
     *
     * @param string $url
     * @param bool   $isAsync Whether or not you want Async loading.
     * @param bool   $isDefer Whether or not you want Defer loading.
     *
     * @return $this
     */
    public function requireJS($url, $isAsync = false, $isDefer = false)
    {
        $this->html->template->appendHTML('HEAD', $this->getTag('script', ['src' => $url, 'defer' => $isDefer, 'async' => $isAsync], '')."\n");

        return $this;
    }

    /**
     * Adds additional CSS stylesheet include in aplication template.
     *
     * @param string $url
     *
     * @return $this
     */
    public function requireCSS($url)
    {
        $this->html->template->appendHTML('HEAD', $this->getTag('link/', ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => $url])."\n");

        return $this;
    }

    /**
     * A convenient wrapper for sending user to another page.
     *
     * @param array|string $page Destination page
     */
    public function redirect($page)
    {
        header('Location: '.$this->url($page));

        $this->run_called = true; // prevent shutdown function from triggering.
        $this->callExit();
    }

    /**
     * Generate action for redirecting user to another page.
     *
     * @param string|array $page Destination URL or page/arguments
     */
    public function jsRedirect($page)
    {
        return new jsExpression('document.location = []', [$this->url($page)]);
    }

    /**
     * Construct HTML tag with supplied attributes.
     *
     * $html = getTag('img/', ['src'=>'foo.gif','border'=>0]);
     * // "<img src="foo.gif" border="0"/>"
     *
     *
     * The following rules are respected:
     *
     * 1. all array key=>val elements appear as attributes with value escaped.
     * getTag('div/', ['data'=>'he"llo']);
     * --> <div data="he\"llo"/>
     *
     * 2. boolean value true will add attribute without value
     * getTag('td', ['nowrap'=>true]);
     * --> <td nowrap>
     *
     * 3. null and false value will ignore the attribute
     * getTag('img', ['src'=>false]);
     * --> <img>
     *
     * 4. passing key 0=>"val" will re-define the element itself
     * getTag('img', ['input', 'type'=>'picture']);
     * --> <input type="picture" src="foo.gif">
     *
     * 5. use '/' at end of tag to close it.
     * getTag('img/', ['src'=>'foo.gif']);
     * --> <img src="foo.gif"/>
     *
     * 6. if main tag is self-closing, overriding it keeps it self-closing
     * getTag('img/', ['input', 'type'=>'picture']);
     * --> <input type="picture" src="foo.gif"/>
     *
     * 7. simple way to close tag. Any attributes to closing tags are ignored
     * getTag('/td');
     * --> </td>
     *
     * 7b. except for 0=>'newtag'
     * getTag('/td', ['th', 'align'=>'left']);
     * --> </th>
     *
     * 8. using $value will add value inside tag. It will also encode value.
     * getTag('a', ['href'=>'foo.html'] ,'click here >>');
     * --> <a href="foo.html">click here &gt;&gt;</a>
     *
     * 9. you may skip attribute argument.
     * getTag('b','text in bold');
     * --> <b>text in bold</b>
     *
     * 10. pass array as 3rd parameter to nest tags. Each element can be either string (inserted as-is) or
     * array (passed to getTag recursively)
     * getTag('a', ['href'=>'foo.html'], [['b','click here'], ' for fun']);
     * --> <a href="foo.html"><b>click here</b> for fun</a>
     *
     * 11. extended example:
     * getTag('a', ['href'=>'hello'], [
     *    ['b', 'class'=>'red', [
     *        ['i', 'class'=>'blue', 'welcome']
     *    ]]
     * ]);
     * --> <a href="hello"><b class="red"><i class="blue">welcome</i></b></a>'
     *
     * @param string|array $tag
     * @param string       $attr
     * @param string|array $value
     *
     * @return string
     */
    public function getTag($tag = null, $attr = null, $value = null)
    {
        if ($tag === null) {
            $tag = 'div';
        } elseif (is_array($tag)) {
            $tmp = $tag;

            if (isset($tmp[0])) {
                $tag = $tmp[0];

                if (is_array($tag)) {
                    // OH a bunch of tags
                    $output = '';
                    foreach ($tmp as $subtag) {
                        $output .= $this->getTag($subtag);
                    }

                    return $output;
                }

                unset($tmp[0]);
            } else {
                $tag = 'div';
            }

            if (isset($tmp[1])) {
                $value = $tmp[1];
                unset($tmp[1]);
            } else {
                $value = null;
            }

            $attr = $tmp;
        }
        if ($tag[0] === '<') {
            return $tag;
        }
        if (is_string($attr)) {
            $value = $attr;
            $attr = null;
        }

        if (is_string($value)) {
            $value = $this->encodeHTML($value);
        } elseif (is_array($value)) {
            $result = [];
            foreach ($value as $v) {
                $result[] = is_array($v) ? $this->getTag(...$v) : $v;
            }
            $value = implode('', $result);
        }

        if (!$attr) {
            return "<$tag>".($value !== null ? $value."</$tag>" : '');
        }
        $tmp = [];
        if (substr($tag, -1) == '/') {
            $tag = substr($tag, 0, -1);
            $postfix = '/';
        } elseif (substr($tag, 0, 1) == '/') {
            return isset($attr[0]) ? '</'.$attr[0].'>' : '<'.$tag.'>';
        } else {
            $postfix = '';
        }
        foreach ($attr as $key => $val) {
            if ($val === false) {
                continue;
            }
            if ($val === true) {
                $tmp[] = "$key";
            } elseif ($key === 0) {
                $tag = $val;
            } else {
                $tmp[] = "$key=\"".$this->encodeAttribute($val).'"';
            }
        }

        return "<$tag".($tmp ? (' '.implode(' ', $tmp)) : '').$postfix.'>'.($value !== null ? $value."</$tag>" : '');
    }

    /**
     * Encodes string - removes HTML special chars.
     *
     * @param string $val
     *
     * @return string
     */
    public function encodeAttribute($val)
    {
        return htmlspecialchars($val);
    }

    /**
     * Encodes string - removes HTML entities.
     *
     * @param string $val
     *
     * @return string
     */
    public function encodeHTML($val)
    {
        return htmlentities($val);
    }

    /**
     * Allow to use semantic-ui-vue components.
     *
     * https://semantic-ui-vue.github.io
     */
    public function useSuiVue()
    {
        if (!$this->is_sui_init) {
            $this->requireJS('https://unpkg.com/semantic-ui-vue/dist/umd/semantic-ui-vue.min.js');
            $this->layout->js(true, (new jsVueService())->useComponent('SemanticUIVue'));
            $this->is_sui_init = true;
        }
    }
}
