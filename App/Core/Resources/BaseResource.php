<?php

namespace App\Core\Resources;

use App\Core\Components\Form;
use App\Core\Dependencies\BaseElement;
use As247\WpEloquent\Database\Eloquent\Model;
use Exception;

abstract class BaseResource extends BaseElement
{
    public array $data = [];
    public string $capability = 'edit_posts';
    public bool $showMenu = false;

    public string $action = '';

    public ?Model $entry = null;

    private string $adminPost = '';
    protected string $formView = __DIR__.'/../Views/form.php';

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->generateRoutes();
        $this->generateActions();

        $this->adminPost = get_admin_url().'admin-post.php';
    }

    protected function redirect($slug)
    {
        $route = 'admin.php?page='.$slug;
        wp_redirect(admin_url($route));
        die();
    }

    private function generateRoutes()
    {
        $pages = $this->getPages();
        $baseSlug = $this->slugify(config('app.slug'), $this->getClass());
       if ($this->showMenu){
            $firstAction = array_keys($pages)[0];
            add_menu_page(
                $this->getMenuTitle(),
                $this->getMenuTitle(),
                $this->capability,
                $baseSlug,
                $pages[$firstAction],
                $this->getMenuIcon(),
                $this->getPosition(),

            );
            array_shift($pages);
        }

        foreach ($pages as $action => $page) {
            $slug = $this->slugify($baseSlug, $action);
            $menuTitle = $this->showMenu?$this->getClass():'';

            add_submenu_page(
                $baseSlug,
                ucfirst($action).' '.$this->getClass(),
                $menuTitle,
                $this->capability,
                $slug,
                $page,
            );
        }
    }

    /**
     * @throws Exception
     */
    public function generateActions()
    {
        $actions = $this->getActions();

        foreach ($actions as $action => $details) {
            if (!isset($details['type']) || !isset($details['action'])) {
                throw new Exception("Action $action doesn't exist");
            }
            add_action($this->slugify($details['type'], config('app.slug'), $this->getClass(),$action), $details['action']);
        }
    }

    public function getPages(): array
    {
        return [];
    }

    public function getActions(): array
    {
        return [];
    }


    /**
     * @throws Exception
     */
    public static function getRoute($action = 'create', $params = []): string
    {
        $queryParams = http_build_query($params);
        $instance = new static();
        $pages = $instance->getPages();

        if (!isset($pages[$action])) {
            throw new Exception("Route $action doesn't exist");
        }

        return admin_url('admin.php?page=' . $instance->slugify(config('app.slug'), $instance->getClass(), $action).'&'.$queryParams);
    }

    /**
     * @throws Exception
     */
    public static function getAction($action = 'save')
    {
        $instance = new static();
        $actions = $instance->getActions();

        if (!isset($actions[$action])) {
            throw new Exception("Action $action doesn't exist");
        }

        return $instance->slugify(config('app.slug'), $instance->getClass(),$action);
    }

    /**
     * @throws Exception
     */
    protected function render()
    {
        $formInstance = new Form();
        $form = $this->form($formInstance);


        if($this->entry != null) {
            if(!@$_SESSION['errors']) {
                $form->fill($this->entry);
            }
            $idField = '<input type="hidden" name="id" value="'.$this->entry->getKey().'"></input>';
        }else{
            $idField = '';
        }

        if(@$_SESSION['errors']){
            $form->fill($_SESSION['form_data']);
            unset($_SESSION['form_data']);
        }

        $adminPost= $this->adminPost;
        $action = $this->action;
        $javascript = $this->javascript();
        $javascript = $this->style();
        require_once $this->formView;

    }

    public function form(Form $form)
    {
        return $form;
    }

    public function javascript()
    {
        return '';
    }

    public function style()
    {
        return '';
    }

    public function setEntry(Model $model)
    {
        $this->entry = $model;

    }

    public function getCurrentAction()
    {
        $action = $_POST['action']??null;
        if($action){
            $action = explode($this->separator, $action)[count(explode($this->separator, $action))-1];
        }
        return $action;
    }

    public function getMenuTitle():?string
    {
        return null;
    }

    public function getMenuIcon():?string
    {
        return null;
    }

    public function getPosition(): ?int
    {
        return null;
    }
}