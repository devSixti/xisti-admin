<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSettings extends Model
{
    protected $table = 'page_settings';

    public function getSlugForCustom($name){
        $slug = trim(strtolower(str_replace(" ","",$name)));
        return $slug;
    }

    public function localized(string $lang = 'es'): self
    {
        $lang = strtolower(substr($lang, 0, 2));
        $clone = clone $this;
        if ($lang === 'en') {
            return $clone;
        }
        $nameField = $lang . '_name';
        $descField = $lang . '_description';
        if (!empty($this->{$descField})) {
            $clone->description = $this->{$descField};
        }
        if (!empty($this->{$nameField})) {
            $clone->name = $this->{$nameField};
        }

        return $clone;
    }
}
