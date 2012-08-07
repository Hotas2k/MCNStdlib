<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 */

/**
 * @namespace
 */
namespace MCN\Controller\HtmlHead;
use Zend\View\Model\ViewModel,
    Zend\Mvc\Controller\AbstractActionController;

class CategoryController extends AbstractActionController
{
    /**
     * @return \MCN\Service\HtmlHead\Category
     */
    protected function getService()
    {
        return $this->getServiceLocator()
                    ->get('mcn.service.html_head.category');
    }

    public function listAction()
    {
        $vm = new ViewModel();
        $vm->setTemplate('mcn/html_head/category/list');
        $vm->setVariable('categories', $this->getService()->fetchAll());

        return $vm;
    }

    public function editAction()
    {
        // Setup the view model
        $vm = new ViewModel();
        $vm->setTemplate('mcn/html_head/category/edit');

        // Get the page
        $category = $this->getService()
                         ->getById($this->params('id'));

        // Uhoh no page?
        if (! $category) {

            // Set 404 response
            $this->response->setStatusCode(404);
            return;
        }

        // Get the form
        $form = $this->getServiceLocator()
                     ->get('mcn_form_html-head');

        // Bind the form to the page
        $form->bind($category);

        // Post request. Woop, Woop!
        if ($this->getRequest()->isPost()) {

            $form->setData($_POST);

            if ($form->isValid()) {

                $this->getService()
                     ->save($category);

                $this->redirect()
                     ->toRoute('admin/html-head/category/preview', $category->toArray());
            }
        }

        return $vm->setVariables(array(
            'form' => $form,
            'page' => $category
        ));
    }

    public function previewAction()
    {
        // Setup the view model
        $vm = new ViewModel();
        $vm->setTemplate('mcn/html_head/category/preview');

        // Get the page
        $category = $this->getService()
                         ->getById($this->params('id'));

        // Uhoh no page?
        if (! $category) {

            // Set 404 response
            $this->response->setStatusCode(404);
            return;
        }

        return $vm->setVariable('category', $category);
    }
}