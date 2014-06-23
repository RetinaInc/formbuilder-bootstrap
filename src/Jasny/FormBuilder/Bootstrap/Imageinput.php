<?php

namespace Jasny\FormBuilder\Bootstrap;

/**
 * Representation of a Jasny Bootstrap file upload widget.
 */
class Imageinput extends Fileinput
{
    /**
     * Create base64 encoded image to embed in HTML 
     * 
     * @param string $file
     * @return string
     */
    protected function createInlineImage($file)
    {
        $picture = file_get_contents($file);
        $size = getimagesize($file);

        // base64 encode the binary data, then break it into chunks according to RFC 2045 semantics
        $base64 = chunk_split(base64_encode($picture));
        return '<img src="data:' . $size['mime'] . ';base64,' . "\n" . $base64 . '" ' . $size[3] . ' />';
    }
    
    /**
     * Render the widget as HTML
     * 
     * @param array $options
     * @param array $attr
     * @return string
     */
    protected function renderElement()
    {
        $options = $this->getOptions();
        
        $hidden = null;
        $image = null;
        
        if (is_array($this->value)) {
            if (!$this->value['error']) {
                $hidden = '<input type="hidden" name="' . htmlentities($attr['name']). '" '
                    . 'value="^;' . htmlentities(join(';', $this->value)) . '">' . "\n";
                $image = $this->createInlineImage($this->value['tmp_name']);
            }
        } elseif ($this->value) {
            $image = '<img src="' . $this->value . '">';
        }

        $name = htmlentities($this->getAttr('name'));
        $attr_html = $this->renderAttrs(['name'=>null]);
        
        $button_select = htmlentities($options['buttons']['select']);
        $button_change = htmlentities($options['buttons']['change']);
        $button_remove = htmlentities($options['buttons']['remove']);

        if (isset($options['holder'])) {
            $thumbnail = '<div class="fileinput-new thumbnail">' . $options['holder'] . '</div>' . "\n"
                . '<div class="fileinput-exists fileinput-preview thumbnail">' . $image . '</div>';
        } else {
            $thumbnail = '<div class="fileinput-preview thumbnail">' . $image . '</div>';
        }
        
        $html = <<<HTML
<div{$attr_html} data-provides="fileinput">
  $thumbnail
  <div>
    <span class="btn btn-file"><span class="fileinput-new">$button_select</span><span class="fileinput-exists">$button_change</span><input type="file" name="$name"/></span>
    <button class="btn fileinput-exists" data-dismiss="fileinput">$button_remove</button>
  </div>
</div>
HTML;
        
        return $html;
    }    
}