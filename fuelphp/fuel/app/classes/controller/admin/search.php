<?php

class Controller_Admin_Search extends Controller_Admin
{

    /**
     * 検索窓をもった index view を作る
     * @param $title
     * @param $model_name
     * @param $page_url
     * @param $filters
     */
    protected function search_index($title, $model_name, $page_url, $search_for, $filters, $order_by = null)
    {
        $table_name = $model_name::table();
        $form = $this->forge_search_form($model_name, $filters);
        $form->repopulate();

        // 違う controller からの処理だったら session を削除する
        $previous_controller = \Fuel\Core\Session::get_flash('previous_controller');
        if ($previous_controller != null and $previous_controller !== get_class($this))
        {
            \Fuel\Core\Session::delete_flash('search');
            foreach ($filters as $label => $column_name)
            {
                \Fuel\Core\Session::delete_flash($column_name);
            }
        }

        // session から検索条件取得
        $search = \Fuel\Core\Session::get_flash('search');
        $filter_values = array();
        foreach ($filters as $label => $column_name)
        {
            $filter_values[$column_name] = \Fuel\Core\Session::get_flash($column_name);
        }

        // GET なら
        if (\Fuel\Core\Input::method() == 'GET')
        {
            // get だったらform の値を再設定
            $this->populate_search_form($form, $search, $filter_values);
        }

        // POST なら
        if(Input::method() == 'POST')
        {
            // validation に引っかかったら処理を止める (view へは method の中で値セットして返している)
            if (!$this->validate_error($form, $table_name, $page_url))
            {
                return;
            }

            // 検索ワード
            if (\Fuel\Core\Input::post('search'))
            {
                $search='%'.Input::post('search').'%';
            }
            else
            {
                // 検索ワードが入力されていない場合
                $search='%%';
            }

            // 条件を form から取得してセットする
            foreach ($filters as $label => $column_name)
            {
                if (\Fuel\Core\Input::post($column_name) != null)
                {
                    $filter_values[$column_name] = \Fuel\Core\Input::post($column_name);
                }
                else
                {
                    if ($model_name::is_peorperty_boolean($column_name))
                    {
                       $filter_values[$column_name] = '0';
                    }
                }
            }

        }

        // 検索条件を作る
        $conditions = $this->forge_search_condition($search, $search_for, $filter_values);

        // order by の設定
        if ($order_by != null)
        {
            $conditions['order_by'] = $order_by;
        }

        // pager 用のこの条件での数を取得するする
        $total = count($model_name::find('all', $conditions));

        // pagination の取得
        $pagination = $this->forge_pagination($page_url . '/index', 3, $total);

        // pager 設定
        $conditions['rows_offset'] = $pagination->offset;
        $conditions['rows_limit'] = $pagination->per_page;

        // query 投げる
        $data[$table_name] = $model_name::find('all', $conditions);
        $data['total'] = $total;

        // relation のデータ取得
        //TODO query の数が多くなるので、opetion で disable に出来るようにする
        foreach ($data[$table_name] as $d)
        {
            foreach($model_name::relations() as $relation_name => $value)
            {
                $class_name = get_class($value);
                if ($class_name === 'Orm\HasOne') //TODO consider to cope with has_many
                {
                    $d[$relation_name] = $d->{$relation_name}->name;
                }
            }
        }

        // controller 個別の validation
        $data[$table_name] = $this->validate_data($data[$table_name]);

        // view に必要なデータを渡す
        $this->template->title = $title;
        $this->template->content = View::forge($page_url . '/index', $data);
        $this->template->content->set_safe('pagination', $pagination);
        $this->template->content->set_safe(array('form' => $form->get_form_elements($page_url)));

        // session に自分の Controller 名前を残す (Controller を跨いで検索条件が残らないように)
        \Fuel\Core\Session::set_flash('previous_controller', get_class($this));
    }

    /**
     * コントローラに紐付いた validation
     */
    protected function validate_data(&$models)
    {
        return $models;
    }

    /**
     * 検索窓を form として作る
     * @param $model_class
     * @param $filters
     * @return mixed
     */
    protected function forge_search_form($model_class, $filters)
    {
        $max_search_word = 512;

        $form = Fieldset_Myfieldset::forge('search');
        $form->add_text('search', 'Search', $max_search_word);

        foreach ($filters as $label => $column_name)
        {
            // 種類を取得
            $ops = array();
            $ops['all'] = 'all';
            foreach ($model_class::get_kinds($column_name) as $k)
            {
                $ops[$k] = $k;
            }

            $form->add_select($column_name, $label, $ops);
        }

        return $form;
    }

    /**
     * search form の初期値を設定する
     * @param $form
     * @param $search
     * @param $filter_values
     */
    protected function populate_search_form(&$form, $search, $filter_values)
    {
        $ops = array();
        $search = str_replace('%', '', $search);
        $ops['search'] = $search;
        foreach ($filter_values as $column_name => $value)
        {
            $ops[$column_name] = $value;
        }

        $form->populate($ops);
    }

    /**
     * Pagination Object を作って返す
     * @param $pagination_url
     * @param $url_segment
     * @param $total
     * @return mixed
     */
    protected function forge_pagination($pagination_url, $url_segment, $total)
    {
        Config::load('pagination', true);
        $config = Config::get('pagination');
        $config['pagination_url'] = $pagination_url;
        $config['uri_segment'] = $url_segment;
        $config['total_items'] = $total;
        return Pagination::forge('pagination', $config);
    }

    /**
     * Validation をして、ひっかかればエラーを返す
     * @param $form
     * @param $table_name
     * @param $page_url
     * @return bool
     * TODO method 名と処理がちゃんと合ってないので、本当はちゃんと切り分けたい
     */
    protected function validate_error(&$form, $table_name, $page_url)
    {
        $val = $form->validation()->add_callable('Validation_Myvalidationrules');
        if (!$val->run()) {
            $data[$table_name] = array();
            $this->template->title = '検索ワード：エラー';
            $this->template->content = View::forge($page_url . '/index', $data);
            $this->template->content->set_safe('html_error', $val->show_errors());
            $this->template->content->set_safe(array('form' => $form->get_form_elements($page_url)));
            return false;
        }
        return true;
    }

    /**
     * 検索条件を作る
     * @param $search 検索文字
     * @param $search_for 検索対象カラム
     * @param $filter_values
     * @return mixed
     */
    private function forge_search_condition($search, $search_for, $filter_values)
    {
        // 検索条件を作る
        $conditions['where'] = array();

        // limit
        $conditions['limit'] = '1000000';

        if ($search != null)
        {
            $conditions['where'][] = array($search_for, 'LIKE', $search);
            \Fuel\Core\Session::set_flash('search', $search);
        }

        foreach ($filter_values as $column_name => $value)
        {
            // 検索条件を追加。all の場合は検索条件に入れない
            if ($value != null and $value !== 'all')
            {
                $conditions['where'][] = array($column_name, $value);
                \Fuel\Core\Session::set_flash($column_name, $value);
            }
        }

        return $conditions;
    }
}
