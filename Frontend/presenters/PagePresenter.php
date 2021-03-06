<?php

namespace FrontendModule\PageModule;

/**
 * Description of PagePresenter
 *
 * @author Tomáš Voslař <tomas.voslar at webcook.cz>
 */
class PagePresenter extends \FrontendModule\BasePresenter{
	
	private $repository;
	
	private $page;
	
	protected function startup() {
		parent::startup();
	
		$this->repository = $this->em->getRepository('WebCMS\PageModule\Entity\Page');
	}

	protected function beforeRender() {
		parent::beforeRender();
		
	}
	
	public function actionDefault($id){
		
		$this->page = $this->repository->findOneBy(array(
			'page' => $this->actualPage
		));
		
		if(!is_object($this->page)){
			$this->page = $this->persistPage();
		}
	}
	
	public function renderDefault($id){
		
		$this->template->photogallery = $this->getPhotogallery($this->page);
		$this->template->page = $this->page;
		$this->template->id = $id;
	}
	
	public function getPhotogallery($page){
		return $this->em->getRepository('WebCMS\PageModule\Entity\Photogallery')->findOneBy(array(
			'page' => $page
		));
	}
	
	private function persistPage(){
		$page = new \WebCMS\PageModule\Entity\Page;
		$page->setText($this->translation['Module page default text.']);
		$page->setPage($this->actualPage);
	
		$this->em->persist($page);
		$this->em->flush();
		
		return $page;
	}
	
	public function textBox($context, $fromPage){
		$page = $context->em->getRepository('WebCMS\PageModule\Entity\Page')->findOneBy(array(
			'page' => $fromPage
		));
		
		$text = "";
		
		if(!$context->settings->get('Hide title', 'pageModule' . $fromPage->getId(), 'checkbox')->getValue()){
			$text = '<h1>' . $fromPage->getTitle() . '</h1>';
		}
		
		$text .= $page->getText();
		
		return $text;
	}
	
	public function photogalleryBox($context, $fromPage){
		
		$page = $context->em->getRepository('WebCMS\PageModule\Entity\Page')->findOneBy(array(
			'page' => $fromPage
		));

		$template = $context->createTemplate();
		$template->page = $page;
		$template->photogallery = $context->em->getRepository('WebCMS\PageModule\Entity\Photogallery')->findOneBy(array('page' => $page));
		
		$template->setFile('../app/templates/page-module/Page/boxes/photogallery.latte');		
		
		
		return $template;
	}
}

?>
