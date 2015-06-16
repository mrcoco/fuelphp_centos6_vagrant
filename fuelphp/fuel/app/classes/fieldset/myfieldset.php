<?php
class Fieldset_Myfieldset extends \Fieldset
{
    /**
     * フォーム要素をviewで使いやすい配列で取得
     */
    public function get_form_elements($open = '', $hidden = array())
    {
        if (is_array($open)) {
            $attr = $open;
        } else {
            $attr['action'] = $open;
            $attr['method'] = 'post';
        }

        // formのIDは'form_' + fieldsetの名前
        $attr['id'] = 'form_' . $this->get_name();

        // CSRFトークンを加え
        $hidden[Config::get('security.csrf_token_key')] = Security::fetch_token();

        // hidden + CSRFトークン生成
        $form['open'] = $this->form()->open($attr, $hidden);

        // 各要素の生成
        foreach ($this->field() as $f) {
            $form[$f->name] = array('label' => $f->label, 'html' => $f->build());
        }

        // close
        $form['close'] = '</form>';

        // エラー取得
        $form['error'] = $this->show_errors();

        return $form;
    }

    /**
     * テキストを受け付ける input text 要素
     * @param $name
     * @param $label
     * @param $max_length
     */
    public function add_text($name, $label, $max_length)
    {
        $this->add($name, $label, array(
            'class' => 'form-control',
        ))
            ->add_rule('trim')
            ->add_rule('no_tab_and_newline')
            ->add_rule('max_length', $max_length);
    }

    /**
     * bootstrap で修飾された select box 要素
     * @param $name
     * @param $label
     * @param $ops
     */
    public function add_select($name, $label, $ops)
    {
        $this->add($name, $label, array(
            'class' => 'form-control',
            'options' => $ops,
            'type' => 'select'
        ))
            ->add_rule('in_array', $ops);
    }

    /**
     * 数値の入力を受け付けるinput text要素
     *
     * - 自動的に数値バリデーションが追加される
     * - 自動的にime-modeがdisabledに設定される
     */
    public function addTextForNumeric($name, $label)
    {
        return $this->add(
            $name,
            $label,
            array(
                'class' => 'input-medium',
                'style' => 'ime-mode:disabled'
            )
        )->add_rule('valid_string', 'numeric')->set_template('{field}');
    }

    /**
     * 選択肢を一行で表示するRadio要素
     */
    public function addRadioInline($name, $label, $options)
    {
        return $this->add(
            $name,
            $label,
            array(
                'type'    => 'radio',
                'options' => $options,
            )
        )->set_template('{fields}<label class="radio inline">{field}{label}</label>{fields}');
    }

    /**
     * 選択肢を改行するRadio要素
     */
    public function addRadioWithBr($name, $label, $options)
    {
        return $this->add(
            $name,
            $label,
            array(
                'type'    => 'radio',
                'options' => $options,
            )
        )->set_template('{fields}<label class="radio">{field}{label}</label>{fields}');
    }

}
