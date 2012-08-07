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

class PageController extends AbstractActionController
{
    /**
     * @return \MCN\Service\HtmlHead\Page
     */
    protected function getService()
    {
        return $this->getServiceLocator()
                    ->get('mcn.service.html_head.page');
    }

    public function listAction()
    {
        $vm = new ViewModel();
        $vm->setTemplate('mcn/html_head/page/list');
        $vm->setVariable('pages', $this->getService()->fetchAll());

        return $vm;
    }

    public function editAction()
    {
        // Setup the view model
        $vm = new ViewModel();
        $vm->setTemplate('mcn/html_head/page/edit');

        // Get the page
        $page = $this->getService()
                     ->getById($this->params('id'));

        // Uhoh no page?
        if (! $page) {

            // Set 404 response
            $this->response->setStatusCode(404);
            return;
        }

        // Get the form
        $form = $this->getServiceLocator()
                     ->get('mcn_form_html-head');

        // Bind the form to the page
        $form->bind($page);

        // Post request. Woop, Woop!
        if ($this->getRequest()->isPost()) {

            $form->setData($_POST);

            if ($form->isValid()) {

                $this->getService()
                     ->save($page);

                $this->redirect()
                     ->toRoute('admin/html-head/page/preview', $page->toArray());
            }
        }

        return $vm->setVariables(array(
            'form' => $form,
            'page' => $page
        ));
    }

    public function previewAction()
    {
        // Setup the view model
        $vm = new ViewModel();
        $vm->setTemplate('mcn/html_head/page/preview');

        // Get the page
        $page = $this->getService()
                     ->getById($this->params('id'));

        // Uhoh no page?
        if (! $page) {

            // Set 404 response
            $this->response->setStatusCode(404);
            return;
        }

        return $vm->setVariable('page', $page);
    }
}