<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Foo extends Controller_Template_Extended {

    public $template = 'templates/main';

    /*
     * You can specify behaviour for your controller here, changing $use_template default field default value like this:
     *
     *   public $use_template = Controller_Template_Extended::TEMPLATE_ONLY;
     *
     * Your actions still can override them for a specific behaviour ( like in action_sub() )
     *
     * Also you can set $content field to a string, so it will be initialized like view (in the same manner $template field is)
     * Or you can initialize it directly in your actions (with view, some raw data etc) if needed:
     *
     *   public $content = 'path/to/some/content_view';
     */

    /**
     * This is example of responding with both template and content (with $this->content being directly assigned in action)
     */
    public function action_bar()
    {
        $this->content = 'Bar';
    }

    /**
     * This is example of responding template if request is initial (and if not we are responding only content)
     */
    public function action_sub()
    {
        $this->use_template = Controller_Template_Extended::ON_INITIAL_REQUEST;
        $this->content = 'Well, here is sub!';
    }

    /**
     * Here is HMVC example - we are requesting other action via HMVC call and it will return only content
     * (because $this->use_template is set to ~::ON_INITIAL_REQUEST in requested action_sub)
     */
    public function action_hmvc()
    {
        $this->content = Request::factory('foo/sub')->execute()->body();
    }

}
