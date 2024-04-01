<?php
class ControllerCatalogFeedback extends Controller
{
    private $error = array();

    public function index()
    {
        $this->language->load('catalog/feedback');
        $this->document->setTitle($this->language
            ->get('heading_feedback'));
        $this->load->model('catalog/feedback');
        $this->getList();
    }

    public function insert() {
        $this->language->load('catalog/feedback');
        $this->document->setTitle($this->language
            ->get('heading_feedback'));
        $this->load->model('catalog/feedback');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_feedback->addfeedback($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['sort'])) {
                $url .='&sort=' . $this->request->get['sort'];}
            if (isset($this->request->get['order'])) {
                $url .='&order=' . $this->request->get['order'];}
            if (isset($this->request->get['page'])) {
                $url .='&page=' . $this->request->get['page'];
            }
            $this->redirect($this->url->link('catalog/feedback',
                'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
        }
        $this->getForm();
    }

    public function update() {
        $this->language->load('catalog/feedback');
        $this->document->setTitle($this->language
            ->get('heading_feedback'));
        $this->load->model('catalog/feedback');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') &&
            $this->validateForm()) {
            $this->model_catalog_feedback->editfeedback($this->request
                ->get['feedback_id'], $this->request->post);
            $this->session->data['success'] = $this->language
                ->get('text_success');
            $url = '';
            if (isset($this->request->get['sort'])) {
                $url .='&sort=' . $this->request->get['sort'];}
            if (isset($this->request->get['order'])) {
                $url .='&order=' . $this->request->get['order'];}
            if (isset($this->request->get['page'])) {
                $url .='&page=' . $this->request->get['page'];}
            $this->redirect($this->url->link('catalog/feedback', 'user_token='
                . $this->session->data['user_token'] . $url, 'SSL'));
        }
        $this->getForm();
    }
    public function delete() {
        $this->language->load('catalog/feedback');
        $this->document->setfeedback_author($this->language
            ->get('heading_feedback'));
        $this->load->model('catalog/feedback');
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $feedback_id) {
                $this->model_catalog_feedback->deletefeedback($feedback_id);
            }
            $this->session->data['success'] = $this->language
                ->get('text_success');
            $url = '';
            if (isset($this->request->get['sort'])) {
                $url .='&sort=' . $this->request->get['sort'];}
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            if (isset($this->request->get['page'])) {
                $url .='&page=' . $this->request->get['page'];
            }
            $this->redirect($this->url->link('catalog/feedback', 'user_token='
                . $this->session->data['user_token'] . $url, 'SSL'));
        }
        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'fd.feedback_author';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/feedback', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['insert'] = $this->url->link('catalog/feedback/insert', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');


//        $data['delete'] = $this->url->link('catalog/feedback/delete', 'user_token=' . $this->sessio->data['user_token'] . $url, 'SSL');


        $data['feedbacks'] = array();

        $data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

//        $feedback_total = $this->model_catalog_feedback->getTotalfeedbacks();
        $feedback_total = 10;


        $results = $this->model_catalog_feedback->getfeedbacks($data);

        foreach ($results as $result) {
            $action = array();
            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('catalog/feedback/update', 'user_token=' .
                    $this->session->data['user_token'] . '&feedback_id=' .
                    $result['feedback_id'] . $url, 'SSL')
            );
            $data['feedbacks'][] = array(
                'feedback_id' => $result['feedback_id'],
                'feedback_author' => $result['feedback_author'],
                'sort_order' => $result['sort_order'],
                'selected' =>isset($this->request->post['selected'])
                    &&in_array($result['feedback_id'], $this->request
                        ->post['selected']),
                'action' => $action
            );
        }

        $data['heading_feedback_author'] = $this->language
            ->get('heading_feedback_author');
        $data['text_no_results'] = $this->language
            ->get('text_no_results');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_title'] = $this->url->link('catalog/feedback', 'user_token=' . $this->session->data['user_token'] . '&sort=id.title' . $url, true);
        $data['sort_sort_order'] = $this->url->link('catalog/feedback', 'user_token=' . $this->session->data['user_token'] . '&sort=i.sort_order' . $url, true);
        $data['sort_noindex'] = $this->url->link('catalog/feedback', 'user_token=' . $this->session->data['user_token'] . '&sort=i.noindex' . $url, true);

        $data['add'] = $this->url->link('catalog/feedback/insert', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('catalog/feedback/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['enabled'] = $this->url->link('catalog/feedback/enable', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['disabled'] = $this->url->link('catalog/feedback/disable', 'user_token=' . $this->session->data['user_token'] . $url, true);


        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = 10;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('catalog/feedback', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($feedback_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($feedback_total - $this->config->get('config_limit_admin'))) ? $feedback_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $feedback_total, ceil($feedback_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/feedback_list', $data));
    }

    protected function getForm() {
        $data['text_form'] = !isset($this->request->get['feedback_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['feedback_author'])) {
            $data['error_feedback_author'] = $this->error['feedback_author'];
        } else {
            $data['error_feedback_author'] = array();
        }

        if (isset($this->error['description'])) {
            $data['error_description'] = $this->error['description'];
        } else {
            $data['error_description'] = array();
        }

        if (isset($this->error['meta_feedback_author'])) {
            $data['error_meta_feedback_author'] = $this->error['meta_feedback_author'];
        } else {
            $data['error_meta_feedback_author'] = array();
        }

        if (isset($this->error['meta_h1'])) {
            $data['error_meta_h1'] = $this->error['meta_h1'];
        } else {
            $data['error_meta_h1'] = array();
        }

        if (isset($this->error['keyword'])) {
            $data['error_keyword'] = $this->error['keyword'];
        } else {
            $data['error_keyword'] = '';
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_feedback_author'),
            'href' => $this->url->link('catalog/feedback', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['feedback_id'])) {
            $data['button_save'] = $this->url->link('catalog/feedback/insert', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('catalog/feedback/edit', 'user_token=' . $this->session->data['user_token'] . '&feedback_id=' . $this->request->get['feedback_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('catalog/feedback', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['feedback_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $feedback_info = $this->model_catalog_feedback->getfeedback($this->request->get['feedback_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['feedback_description'])) {
            $data['feedback_description'] = $this->request->post['feedback_description'];
        } elseif (isset($this->request->get['feedback_id'])) {
            $data['feedback_description'] = $this->model_catalog_feedback->getfeedbackDescriptions($this->request->get['feedback_id']);
        } else {
            $data['feedback_description'] = array();
        }

        $data['heading_feedback_author'] = $this->language->get('heading_feedback_author');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_enabled'] = $this->language->get('text_enabled');

        $language_id = $this->config->get('config_language_id');
        if (isset($data['feedback_description'][$language_id]['feedback_author'])) {
            $data['heading_feedback_author'] = $data['feedback_description'][$language_id]['feedback_author'];
        }

        $this->load->model('setting/store');

        $data['stores'] = array();

        $data['stores'][] = array(
            'store_id' => 0,
            'name'     => $this->language->get('text_default')
        );

        $stores = $this->model_setting_store->getStores();

        foreach ($stores as $store) {
            $data['stores'][] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name']
            );
        }

        if (isset($this->request->post['feedback_store'])) {
            $data['feedback_store'] = $this->request->post['feedback_store'];
        } elseif (isset($this->request->get['feedback_id'])) {
            $data['feedback_store'] = $this->model_catalog_feedback->getfeedbackStores($this->request->get['feedback_id']);
        } else {
            $data['feedback_store'] = array(0);
        }

        /*if (isset($this->request->post['bottom'])) {
            $data['bottom'] = $this->request->post['bottom'];
        } elseif (!empty($feedback_info)) {
            $data['bottom'] = $feedback_info['bottom'];
        } else {
            $data['bottom'] = 0;
        }*/

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($feedback_info)) {
            $data['status'] = $feedback_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['noindex'])) {
            $data['noindex'] = $this->request->post['noindex'];
        } elseif (!empty($feedback_info)) {
            $data['noindex'] = $feedback_info['noindex'];
        } else {
            $data['noindex'] = 1;
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($feedback_info)) {
            $data['sort_order'] = $feedback_info['sort_order'];
        } else {
            $data['sort_order'] = '';
        }

        if (isset($this->request->post['feedback_seo_url'])) {
            $data['feedback_seo_url'] = $this->request->post['feedback_seo_url'];
        } elseif (isset($this->request->get['feedback_id'])) {
            $data['feedback_seo_url'] = $this->model_catalog_feedback->getfeedbackSeoUrls($this->request->get['feedback_id']);
        } else {
            $data['feedback_seo_url'] = array();
        }

        if (isset($this->request->post['feedback_layout'])) {
            $data['feedback_layout'] = $this->request->post['feedback_layout'];
        } elseif (isset($this->request->get['feedback_id'])) {
            $data['feedback_layout'] = $this->model_catalog_feedback->getfeedbackLayouts($this->request->get['feedback_id']);
        } else {
            $data['feedback_layout'] = array();
        }

        $this->load->model('design/layout');

        $data['layouts'] = $this->model_design_layout->getLayouts();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/feedback_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'catalog/feedback')) {
            $this->error['warning'] = $this->language
                ->get('error_permission');
        }
        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language
                ->get('error_warning');
        }
        if (!$this->error) {return true;} else {return false;}
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'catalog/feedback')) {
            $this->error['warning'] = $this->language
                ->get('error_permission');
        }
        if (!$this->error) {return true;} else {return false;}
    }


}
