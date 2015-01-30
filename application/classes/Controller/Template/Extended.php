<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class Controller_Template_Extended
 *
 * This class provides some additional separation: you have template view (from Controller_Template)
 * and content data (not necessary a view).
 *
 * Content data is passed to template if $use_template == ~::TEMPLATE_YES
 * or if $use_template == ~::ON_INITIAL_REQUEST and incoming request is initial.
 * If not, content data is responsed directly without template.
 * If $use_template is set to ~::TEMPLATE_ONLY, then class will behave like Controller_Template
 * (ignoring $content field)
 *
 * It allows you to separate template and content, so that content will be responsed either in the template
 * or directly (useful, when initial requests must get responses with template+content and HMVC requests
 * must get responses with content only (being a view or JSON data etc.)
 *
 * Set $use_template = false, so that template will not be used (for all class actions or directly in some actions)
 *
 * @author deus krid
 */
abstract class Controller_Template_Extended extends Controller_Template {

    /**
     * Template is not rendered, content is responsed directly
     */
    const TEMPLATE_NO = 0;
    /**
     * Content is passed to template, template is responsed
     */
    const TEMPLATE_YES = 1;
    /**
     * Same as TEMPLATE_YES, but only if incoming request is initial (instead of being HMVC subrequest)
     */
    const ON_INITIAL_REQUEST = 2;
    /**
     * Use only template (skip $content field, use Controller_Template behavior)
     */
    const TEMPLATE_ONLY = 3;

    /**
     * @var mixed if string, then it is initialized as view in before() (like $template in Controller_Template). If false, then you need to put some data there in your action manually.
     * Passed to template under name 'content'
     */
    public $content = false;

    /**
     * @var int template using logic (to use it, not to use it, use it only on initial requests, behave like controller_template)
     */
    public $use_template = Controller_Template_Extended::TEMPLATE_YES;

    /**
     * Calls before action
     */
    public function before()
    {
        if ($this->content !== false)
            $this->content = View::factory($this->content);

        parent::before();
    }

    /**
     * Calls after action
     */
    public function after()
    {
        if ( ($this->use_template == Controller_Template_Extended::TEMPLATE_YES) || ( ($this->use_template == Controller_Template_Extended::ON_INITIAL_REQUEST) && $this->request->is_initial()) )
            // ... then we need to response both template and content
            $this->template->content = $this->content;
        elseif ($this->use_template != Controller_Template_Extended::TEMPLATE_ONLY) // ... then we need to response $this->content only
            if ($this->content instanceof View)
                // to keep original Controller_Template logic (with AUTO_RENDER check) available we are passing $this->content as $this->template (if it is View instance and can be rendered)
                $this->template = $this->content;
            else
            {
                // and if not we are responsing $this->content directly and halting after(), so that parent::after() below will not be called
                $this->response->body($this->content);
                return;
            }

        /*
         * If $this->use_template == ~::TEMPLATE_ONLY, then non of above conditions will trigger and only parent::after() call will be executed
         * (which is normal Controller_Template behavior, ignoring $this->content
         */

        parent::after();
    }

}
