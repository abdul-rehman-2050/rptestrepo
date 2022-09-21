<?php
namespace App\Http\Traits;

trait Searchable
{
    
    public function scopeSearch($query, $limit = null, $query_input = 'query')
    {
        $query_string = request()->get($query_input);
        $words = explode(' ', $query_string);
        
        $searchables = isset($this->searchables) ? $this->searchables:[];

        foreach ($words as $word) {
            $query = $query->where(function ($q) use ($word, $searchables) {
                foreach($searchables as $key => $searchable)
                {
                    if(is_array($searchable)) {
                        if( $key == 0 )
                            $q->whereRaw("REPLACE(".$searchable['column'].", ' ', '') like ?", ["%{$word}%"]);
                        else
                            $q->orWhereRaw("REPLACE(".$searchable['column'].", ' ', '') like ?", ["%{$word}%"]);
                    }else{
                        if( $key == 0 )
                            $q->where($searchable, 'like', "%{$word}%");
                        else
                            $q->orWhere($searchable, 'like', "%{$word}%");
                    }
                    
                }
            });
        }

        

        if( is_null($limit) )
            return $query;

        return $query->take($limit);
    }


    public function scopeDataTable($query)
    {
        $page = 1;
        $itemsPerPage = 25;
        $sortBy = null;
        $sortDirection = 'asc';


        if( $pagination = request()->pagination )
        {
            $pagination = json_decode($pagination, true);
            $page = isset($pagination['page']) ?$pagination['page']:1;
            $itemsPerPage = isset($pagination['itemsPerPage']) ?$pagination['itemsPerPage']:25;
            $sortBy = isset($pagination['sortBy']) ? $pagination['sortBy']:null;
            
            if( isset($pagination['descending']) and $pagination['descending'] )
            {
                $sortDirection = 'desc';
            }
        }

        $skip = $itemsPerPage * ($page-1);
        

        $query = $query->search($itemsPerPage);
        
        $query_all = clone $query;
        
        $query = $query->skip($skip);

        if( $sortBy )
        {
            $query = $query->orderBy($sortBy, $sortDirection);
        }
        

        $data = $query->get();

        return [
            'items' => $data,
            'total' => $query_all->count()
        ];
    }
}