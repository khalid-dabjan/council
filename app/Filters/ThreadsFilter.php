<?php

namespace App\Filters;

/**
 * Description of ThreadsFilter
 *
 * @author khaliddabjan
 */
class ThreadsFilter extends Filter
{

    protected $filters = ['by', 'popular', 'unanswered'];

    /**
     * 
     * @param type $username
     * @return type
     */
    protected function by($username)
    {
        $user = \App\User::where('name', $username)->firstOrFail();

        return $this->builder->where('user_id', $user->id);
    }

    public function popular($value)
    {
        $this->builder->getQuery()->orders = [];
        return $this->builder->orderBy('replies_count', 'DESC');
    }

    public function unanswered($value)
    {
        return $this->builder->where('replies_count', 0);
    }

}
