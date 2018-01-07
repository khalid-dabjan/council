<?php

namespace App\Filters;

/**
 * Description of Filter
 *
 * @author khaliddabjan
 */
abstract class Filter
{

    protected $request, $builder;
    protected $filters = [];

    public function __construct(\Illuminate\Http\Request $request)
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value) {

            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        return $this->builder;
    }

    protected function getFilters()
    {
        return array_filter($this->request->only($this->filters));
    }

}
