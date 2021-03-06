<?php

namespace App\Http\Controllers;

use App\Entities\Repos\PageRepo;
use App\Exceptions\NotFoundException;
use Illuminate\Http\Request;

class PageTemplateController extends Controller
{
    protected $pageRepo;

    /**
     * PageTemplateController constructor
     */
    public function __construct(PageRepo $pageRepo)
    {
        $this->pageRepo = $pageRepo;
        parent::__construct();
    }

    /**
     * Fetch a list of templates from the system.
     */
    public function list(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $templates = $this->pageRepo->getTemplates(10, $page, $search);

        if ($search) {
            $templates->appends(['search' => $search]);
        }

        return view('pages.template-manager-list', [
            'templates' => $templates
        ]);
    }

    /**
     * Get the content of a template.
     * @throws NotFoundException
     */
    public function get(int $templateId)
    {
        $page = $this->pageRepo->getById($templateId);

        if (!$page->template) {
            throw new NotFoundException();
        }

        return response()->json([
            'html' => $page->html,
            'markdown' => $page->markdown,
        ]);
    }
}
