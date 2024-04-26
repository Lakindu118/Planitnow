<?php
// no direct access
defined('ABSPATH') || die();

if(!class_exists('LSD_IX_Template')):

/**
 * Listdom IX Template.
 * Used in CSV and Excel
 *
 * @class LSD_IX_Template
 * @version	1.0.0
 */
abstract class LSD_IX_Template
{
    protected $key;

    public function all()
    {
        return get_option($this->key, []);
    }

    public function get($key)
    {
        $templates = $this->all();
        return $templates[$key] ?? [];
    }

    public function add($template = [])
    {
        $templates = $this->all();
        $templates = [time() => $template] + $templates;

        $this->save($templates);
    }

    /**
     * @param $templates
     * @return void
     */
    public function save($templates)
    {
        update_option($this->key, $templates, false);
    }

    /**
     * @param array $args
     * @return false|string
     */
    public function dropdown(array $args)
    {
        $templates = $this->all();

        $options = [];
        foreach($templates as $key => $template)
        {
            $options[$key] = $template['name'];
        }

        $args['options'] = $options;
        return LSD_Form::select($args);
    }
}

endif;