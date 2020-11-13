<?php

namespace App\Support;

trait HasAdvancedFilter
{
    public function scopeAdvancedFilter($query)
    {
        return $this->processQuery($query, request()->all())
            ->orderBy(request('sort', 'id'), request('order', 'desc'))
            ->paginate(request('limit', 10));
    }

    public function processQuery($query, $data)
    {
        foreach($this->filterable as $filter){
            if(!empty($data[$filter])){
                $query->where($filter,'=',$data[$filter]);
            }
        }
        return $query;
    }
}
