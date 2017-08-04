<?php 
class ControllerFeedSeoPackageSitemap extends Controller {
	private $error = array(); 

	public function __construct($registry) {
		define('OC_VERSION', (int) str_replace('.', '', substr(VERSION, 0, 5)));
		define('OC_V151', substr(VERSION, 0, 5) == '1.5.1');
		define('OC_V2', substr(VERSION, 0, 1) == 2);
		parent::__construct($registry);
	}

	public function index() {
    $this->load->model('user/user_group');

    $this->model_user_user_group->addPermission(version_compare(VERSION, '2.0.2', '>=') ? $this->user->getGroupId() : 1, 'access', 'feed/seopackage_sitemap');
    $this->model_user_user_group->addPermission(version_compare(VERSION, '2.0.2', '>=') ? $this->user->getGroupId() : 1, 'modify', 'feed/seopackage_sitemap');
    
		$data['_language'] = &$this->language;
		$data['_config'] = &$this->config;
		$data['_url'] = &$this->url;
		$data['token'] = $this->session->data['token'];
		
    $this->load->model('localisation/language');
    $languages = $data['languages'] = $this->model_localisation_language->getLanguages();

    foreach ($data['languages'] as &$language) {
      if (version_compare(VERSION, '2.2', '>=')) {
        $language['image'] = 'language/'.$language['code'].'/'.$language['code'].'.png';
      } else {
        $language['image'] = 'view/image/flags/'. $language['image'];
      }
    }
    
		if (!OC_V2) {
			$this->document->addStyle('view/seo_package/awesome/css/font-awesome.min.css');
			$this->document->addStyle('view/seo_package/bootstrap.min.css');
			$this->document->addStyle('view/seo_package/bootstrap-theme.min.css');
			$this->document->addScript('view/seo_package/bootstrap.min.js');
		}
		$this->document->addStyle('view/seo_package/style.css');

		$this->language->load('feed/seopackage_sitemap');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));

    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];
      unset($this->session->data['success']);
    } else $data['success'] = '';
    
    if (isset($this->session->data['error'])) {
      $data['error'] = $this->session->data['error'];
      unset($this->session->data['error']);
    } elseif (!empty($this->error['error'])) {
      $data['error'] = $this->error['error'];
    } else {
      $data['error'] = '';
    }
    
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('seopackage_sitemap', $this->request->post);				

			$this->session->data['success'] = $this->language->get('text_success');

			if(method_exists($this, 'redirect')) {
				$this->redirect($this->url->link('feed/seopackage_sitemap', 'token=' . $this->session->data['token'], 'SSL'));
			} else {
				$this->response->redirect($this->url->link('feed/seopackage_sitemap', 'token=' . $this->session->data['token'], 'SSL'));
			}
		}

    // multi-stores
		$this->load->model('setting/store');
		$data['stores'] = array();
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->config->get('config_name'),
			'url'     => HTTP_CATALOG,
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$action = array();

			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name'],
        'url'     => $store['url'],
			);
		}
    
		$data['heading_title'] = $this->language->get('module_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_data_feed'] = $this->language->get('entry_data_feed');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

    if (version_compare(VERSION, '2.3', '>=')) {
      $data['breadcrumbs'][] = array(
        'text'      => $this->language->get('text_feed'),
        'href'      => $this->url->link('extension/extension', 'type=feed&token=' . $this->session->data['token'], 'SSL'),       		
        'separator' => ' :: '
      );
    } else {
      $data['breadcrumbs'][] = array(
        'text'      => $this->language->get('text_feed'),
        'href'      => $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'),       		
        'separator' => ' :: '
      );
    }

		$data['breadcrumbs'][] = array(
			'text'      => strip_tags($this->language->get('heading_title')),
			'href'      => $this->url->link('feed/seopackage_sitemap', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['action'] = $this->url->link('feed/seopackage_sitemap', 'token=' . $this->session->data['token'], 'SSL');

    if (version_compare(VERSION, '2.3', '>=')) {
      $data['cancel'] = $this->url->link('extension/extension', 'type=feed&token=' . $this->session->data['token'], 'SSL');
    } else {
      $data['cancel'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL');
    }

		if (isset($this->request->post['seopackage_sitemap_status'])) {
			$data['seopackage_sitemap_status'] = $this->request->post['seopackage_sitemap_status'];
		} else {
			$data['seopackage_sitemap_status'] = $this->config->get('seopackage_sitemap_status');
		}

		$data['data_feed'] = 'index.php?route=feed/seopackage_sitemap';
    
		if (OC_V2) {
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			
			$this->response->setOutput($this->load->view('feed/seopackage_sitemap.tpl', $data));
		} else {
			$data['column_left'] = '';
			$this->data = &$data;
			$this->template = 'feed/seopackage_sitemap.tpl';
			$this->children = array(
				'common/header',
				'common/footer'
			);
					
			$this->response->setOutput($this->render());
		}
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'feed/seopackage_sitemap')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}	
}
?>