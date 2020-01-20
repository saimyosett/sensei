<?php¥

namespace App\Entities;

use App\Entities\Managers\EntityContext;
use Illuminate\View\View;

class BreadcrumbsViewComposer
{

    protected $entityContextManager;

    /**
     * BreadcrumbsViewComposer constructor.
     * @param EntityContext $entityContextManager
     */
    public function __construct(EntityContext $entityContextManager)
    {
        $this->entityContextManager = $entityContextManager;
    }

    /**
     * Modify data when the view is composed.
     * @param View $view
     */
    public function compose(View $view)
    {
        $crumbs = $view->getData()['crumbs'];
        $firstCrumb = $crumbs[0] ?? null;
        if ($firstCrumb instanceof Book) {
            $shelf = $this->entityContextManager->getContextualShelfForBook($firstCrumb);
            if ($shelf) {
                array_unshift($crumbs, $shelf);
                $view->with('crumbs', $crumbs);
            }
        }
    }
}
